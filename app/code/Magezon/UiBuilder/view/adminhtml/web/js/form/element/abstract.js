define([
    'ko',
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract'
], function (ko, registry, Abstract) {
    'use strict';

    ko.virtualElements.allowedBindings.afterRender = true;

    return Abstract.extend({
        defaults: {
            observableVariables: [],
            listens: {
                '${ $.provider }:data.${ $.parentScope }.beforeOpenModal': 'beforeOpenModal',
                '${ $.provider }:data.${ $.parentScope }.afterOpenModal': 'afterOpenModal',
                '${ $.provider }:data.${ $.parentScope }.beforeCloseModal': 'beforeCloseModal',
                '${ $.provider }:data.${ $.parentScope }.afterCloseModal': 'afterCloseModal',
                '${ $.provider }:data.${ $.parentScope }.beforeLoadData': 'beforeLoadData',
                '${ $.provider }:data.${ $.parentScope }.afterLoadData': 'afterLoadData',
                '${ $.provider }:data.${ $.parentScope }.beforeSaveValues': 'beforeSaveValues',
                '${ $.provider }:data.${ $.parentScope }.afterSaveValues': 'afterSaveValues'
            }
        },

        /**
         * Initializes observable properties of instance
         */
        initObservable: function () {

            this._super();

            if (this.observableVariables) {
                for (var i = 0; i < this.observableVariables.length; i++) {
                    this.observe(this.observableVariables[i]);
                }
            }

            return this;
        },

        beforeOpenModal: function () {

        },

        afterOpenModal: function () {

        },

        beforeCloseModal: function () {

        },

        afterCloseModal: function () {

        },

        beforeLoadData: function () {

        },

        afterLoadData: function () {

        },

        beforeSaveValues: function () {

        },

        afterSaveValues: function () {

        }
    });
});
