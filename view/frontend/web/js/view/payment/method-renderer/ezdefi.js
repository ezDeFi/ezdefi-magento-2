define([
        'jquery',
        'ko',
        'Magento_Checkout/js/view/payment/default',
        'mage/storage',
        'mage/url'
    ],
    function ($, ko, Component, storage, url) {
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

        return Component.extend({
            redirectAfterPlaceOrder: false,

            abcd: ko.observable('hidden'),

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
                console.log(window.checkoutConfig);
                return window.checkoutConfig.currencies;
            },

            createPayment: function (paymentType = null, callback) {
                this.renderCurrency();
                this.isPlaceOrder(true);
                this.isShowPaymentContent(true);
                $(selectors.selectCurrencyBox).css('display', 'none');
                
                var that = this;
                let urlCreatePayment = url.build('ezdefi/frontend/createpayment');
                let currencyId = $(".ezdefi__select-currency--checkbox:checked").val();

                if(paymentType == null) {
                    paymentType = 'simple'
                }

                storage.post(
                    urlCreatePayment,
                    JSON.stringify({
                        type: paymentType,
                        currency_id: currencyId
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
                // this.checkOrderComplete();
            },

            createEzdefiPayment: function() {
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
                    }
                }, 1000);
            },

            afterPlaceOrder: function() {
                this.checkOrderComplete();
                this.createPayment();
            },

            checkOrderComplete: function () {
                let urlCheckOrderComplete = url.build('ezdefi/frontend/checkordercomplete');
                storage.get(
                    urlCheckOrderComplete,
                    JSON.stringify({}),
                    true
                ).done(function(response) {
                    let orderStatus = response.orderStatus;
                    if(orderStatus === 'processing') {
                        window.location.href = url.build('');
                    }
                });
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
        });
    }
);