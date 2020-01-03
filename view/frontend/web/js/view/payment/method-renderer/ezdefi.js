define([
        'jquery',
        'Magento_Checkout/js/view/payment/default'
    ],
    function ($, Component) {
        'use strict';

        console.log("ezdefi-method");

        return Component.extend({
            defaults: {
                template: 'Ezdefi_PaymentMethod/payment/ezdefi'
            },

            context: function() {
                return this;
            },

            getCode: function() {
                return 'ezdefi_paymentmethod';
            },

            isActive: function() {
                return true;
            }
        });
    }
);