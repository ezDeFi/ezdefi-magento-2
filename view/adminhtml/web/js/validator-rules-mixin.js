define([
    'jquery'
], function ($) {
    'use strict';

    return function (target) {
        console.log(target);
        $.validator.addMethod(
            'validate-greater-than-one',
            function (value) {
                return !(value <= 1);
            },
            $.mage.__('Please enter a number 2 or greater in this field.')
        );

        return target;
    };
});