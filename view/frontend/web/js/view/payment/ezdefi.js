define([
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';

        console.log("ezdefi");

        rendererList.push(
            {
                type: 'ezdefi_payment',
                component: 'Ezdefi_Payment/js/view/payment/method-renderer/ezdefi'
            }
        );

        /** Add view logic here if needed */
        return Component.extend({});
    });