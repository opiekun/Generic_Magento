define([
        'jquery',
        'uiComponent',
        'ko'
    ], function ($, Component, ko) {
        'use strict';
        return Component.extend({
        	defaults: {
                template: 'Clearsale_Integration/custom-block'
            },
            initialize: function () {
                this._super();
            },
            getFingerprintScript: function () {
                return window.checkoutConfig.script;
            },
            getTmTags: function () {
                return window.checkoutConfig.tmtags;
            }
        });
    }
);