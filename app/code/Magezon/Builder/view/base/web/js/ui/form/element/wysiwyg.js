define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/textarea'
], function ($, _, Textarea) {
    'use strict';

    return Textarea.extend({

        defaults: {
            elementTmpl: 'Magezon_Builder/ui/form/element/builder'
        },

        initialize: function () {
            this._super();

            return this;
        }
    })
});