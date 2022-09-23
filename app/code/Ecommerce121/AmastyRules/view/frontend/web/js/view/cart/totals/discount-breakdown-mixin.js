/**
 * Copyright (c) 2020 121eCommerce (https://www.121ecommerce.com/)
 */

define([
    'jquery'
], function () {
    'use strict';

    return function (target) {
        return target.extend({
            defaults: {
                template: 'Ecommerce121_AmastyRules/summary/discount-breakdown',
                rules: false,
                cartSelector: '.cart-summary tr.totals',
                checkoutSelector: '.totals.discount'
            },
        });
    };
});
