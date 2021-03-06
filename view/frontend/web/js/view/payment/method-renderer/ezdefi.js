define([
        'jquery',
        'ko',
        'Magento_Checkout/js/view/payment/default',
        'mage/storage',
        'Magento_Checkout/js/model/quote',
        'mage/url'
    ],
    function ($, ko, Component, storage, quote, url) {
        'use strict';

        var selectors = {
            selectCurrencyRadio: '.ezdefi__select-currency--checkbox',
            selectCurrencyLabel: '.ezdefi__select-currency--label',
            buttonCreatePayment: '.ezdefi__btn-create-payment',
            selectCurrencyBox  : '.ezdefi__select-currency-box'
        }

        $(document).on('change', selectors.selectCurrencyRadio, function () {
            let inputId = $(selectors.selectCurrencyRadio +":checked").attr('id');
            $(selectors.selectCurrencyLabel).css('border','1px solid #d8d8d8').css('background', 'inherit');
            $(selectors.selectCurrencyLabel + "[for='"+inputId+"']").css('border', '2px solid #54bdff').css('background', '#c0dcf9db');
            $(selectors.buttonCreatePayment).prop('disabled', false);
        });
        let renderPrice = function () {
            storage.post(
                url.build('ezdefi/frontend/getCurrencies'),
                JSON.stringify({}),
                true
            ).done(function(response) {
                let currencies = response.currencies;
                for(let i in currencies) {
                    let currency = currencies[i];
                    $('#currency-price-' + currency._id).html(currency.token.price);
                }
            });
        }
        renderPrice();

        return Component.extend({
            redirectAfterPlaceOrder: false,

            simplePaymentContent: ko.observable(''),
            ezdefiPaymentContent: ko.observable(''),
            isShowPaymentContent: ko.observable(false),
            isPlaceOrder        : ko.observable(false),
            currencyChoosed     :  {
                logo            : ko.observable(''),
                name            : ko.observable(''),
                discount        : ko.observable('')
            },
            showingEzdefiPayment: ko.observable(false),
            showingSimplePayment: ko.observable(false),
            countDownInterval   : {
                ezdefi          : null,
                simple          : null
            },
            loadedCurrencies: ko.observable(false),
            currencies: null,

            defaults: {
                template: 'Ezdefi_Payment/payment/ezdefi'
            },

            getCode: function() {
                return 'ezdefi_payment';
            },

            isActive: function() {
                return true;
            },

            getCurrencies : function () {
                return window.checkoutConfig.currencies;
            },

            getEzdefiLogo: function () {
                return window.checkoutConfig.ezdefiLogo;
            },

            getPaymentTitle() {
                var element = ' Pay with cryptocurrencies';
                var currenies = this.getCurrencies();
                for(let i = 0; i<3; i++) {
                    if (currenies[i]) {
                        element += '<span><img src="'+currenies[i].token.logo+'" style="width: 20px; height: 20px; margin-left: 4px"></span>'
                    }
                }
                if(currenies.length > 3) {
                    element += '<span>...</span>'
                }
                return element;
            },

            checkEnableSimpleMethod: function () {
                return window.checkoutConfig.simpleMethod === true;
            },

            checkEnableEzdefiMethod: function () {
                return window.checkoutConfig.ezdefiMethod === true;
            },

            isHasOneMethod: function () {
                if(this.checkEnableEzdefiMethod() && !this.checkEnableSimpleMethod()) {
                    return true;
                } else if(!this.checkEnableEzdefiMethod() && this.checkEnableSimpleMethod()) {
                    return true;
                }
                return false;
            },

            createPayment: function (paymentType = null) {
                this.renderCurrency();
                this.isPlaceOrder(true);
                this.isShowPaymentContent(true);
                $(selectors.selectCurrencyBox).css('display', 'none');
                
                var that = this;
                let urlCreatePayment = url.build('ezdefi/frontend/createpayment');
                let coinId = $(".ezdefi__select-currency--checkbox:checked").val();

                storage.post(
                    urlCreatePayment,
                    JSON.stringify({
                        type: paymentType,
                        coin_id: coinId
                    }),
                    true
                ).done(function(response) {
                    if(paymentType === 'simple') {
                        that.simplePaymentContent(response);
                    } else if (paymentType === 'ezdefi') {
                        that.ezdefiPaymentContent(response);
                    }
                    that.createCountDownSimpleMethod(paymentType)
                });
            },

            createSimplePayment:function() {
                this.showingEzdefiPayment(true);
                $(".btn-choose-payment-type").removeClass('ezdefi__check-showed-payment');
                $(".btn-show-payment--simple").addClass('ezdefi__check-showed-payment');
                $(".payment-box").css('display', 'none');
                $(".simple-pay-box").css('display', 'block');
                if(!this.simplePaymentContent()) {
                    this.createPayment('simple');
                }
            },

            createEzdefiPayment: function() {
                console.log('ezdefi payment');
                this.showingSimplePayment(true);
                $(".btn-choose-payment-type").removeClass('ezdefi__check-showed-payment');
                $(".btn-show-payment--ezdefi").addClass('ezdefi__check-showed-payment');
                $(".payment-box").css('display', 'none');
                $(".ezdefi-pay-box").css('display', 'block');
                if(!this.ezdefiPaymentContent()) {
                    this.createPayment('ezdefi');
                }
            },

            createCountDownSimpleMethod: function(type) {
                var that = this;
                var expiredTime = $("#ezdefi__payment-expiration--" + type).val();
                this.countDownInterval[type] = setInterval(function () {
                    var timestampCountdown = new Date(expiredTime) - new Date();
                    var secondToCountdown = Math.floor(timestampCountdown / 1000);
                    if (secondToCountdown >= 0) {
                        var hours = Math.floor(secondToCountdown / 3600);
                        secondToCountdown %= 3600;
                        var minutes = Math.floor(secondToCountdown / 60);
                        var seconds = secondToCountdown % 60;
                        if (hours > 0) {
                            $("#ezdefi__countdown-label--" + type).html(hours + ':' + minutes + ':' + seconds);
                        } else {
                            $("#ezdefi__countdown-label--" + type).html(minutes + ':' + seconds);
                        }
                    } else {
                        $("#ezdefi__countdown-label--" + type).html('0:0');
                        clearInterval(that.countDownInterval[type]);
                        that.showTimeoutMesage(type);
                    }
                }, 1000);
            },

            showTimeoutMesage: function(type) {
                var that = this;
                var timeoutNotify = $('.timeout-notification--' + type);
                var qrcodeImage = $('.ezdefi-payment__qr-code--' + type);
                timeoutNotify.css('display', 'block');
                qrcodeImage.css('filter', 'blur(4px)');

                timeoutNotify.click(function () {
                    if (type == 'simple') {
                        that.simplePaymentContent('');
                    } else {
                        that.ezdefiPaymentContent('');
                    }
                    that.createPayment(type);
                });
            },

            afterPlaceOrder: function() {
                this.checkOrderComplete();
                this.checkCreatePaymentAfterCheckType();
            },

            checkCreatePaymentAfterCheckType: function() {
                if(this.checkEnableSimpleMethod()) {
                    this.createSimplePayment()
                } else {
                    this.createEzdefiPayment();
                    $(".btn-choose-payment-type").removeClass('ezdefi__check-showed-payment');
                    $(".btn-show-payment--ezdefi").addClass('ezdefi__check-showed-payment');
                    $(".payment-box").css('display', 'none');
                    $(".ezdefi-pay-box").css('display', 'block');
                }
            },

            checkOrderComplete: function () {
                var checkOrderCompleteInterval = setInterval(function () {
                    let urlCheckOrderComplete = url.build('ezdefi/frontend/checkordercomplete');
                    storage.get(
                        urlCheckOrderComplete,
                        JSON.stringify({}),
                        true
                    ).done(function(response) {
                        let orderStatus = response.orderStatus;
                        if(orderStatus === 'processing') {
                            clearInterval(checkOrderCompleteInterval);
                            window.location.href = url.build('');
                        }
                    });
                }, 1000);
            },

            renderCurrency: function() {
                var currrency = $(".ezdefi__select-currency--checkbox:checked");
                this.currencyChoosed.name(currrency.data('name'));
                this.currencyChoosed.discount(currrency.data('discount'));
                this.currencyChoosed.logo(currrency.data('logo'));
            },

            changeCurrency: function () {
                $(selectors.selectCurrencyBox).css('display', 'block');
                $("#ezdefi__select-currency--checkbox").prop('checked', false);
                clearInterval(this.countDownInterval['simple'])
                clearInterval(this.countDownInterval['ezdefi'])
                this.isShowPaymentContent(false);
                this.simplePaymentContent('');
                this.ezdefiPaymentContent('');
            },

            testCheck: function () {
                var method = $('input[name="choose-method-radio"]:checked').data('method');
                if(method === 'ezdefi') {
                    this.createEzdefiPayment()
                } else if (method === 'simple') {
                    this.createSimplePayment();
                }

            }
        });
    }
);