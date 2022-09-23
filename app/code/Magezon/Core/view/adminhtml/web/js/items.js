define([
    'Magento_Ui/js/form/element/ui-select'
], function (Component) {
    'use strict';

    var itemId = window.mgzReportsConfig.item_id ? window.mgzReportsConfig.item_id : '';

    return Component.extend({

    	defaults: {
            value: itemId,
    		multiple: false,
            disableLabel: true,
            filterOptions: true,
            template: 'ui/grid/filters/elements/ui-select',

            exports: {
                value: '${ $.provider }:params.item_id'
            }
    	},

    	initConfig: function (config) {

            config['options'] = window.mgzReportsConfig.items;

            this._super(config);

            return this;
    	}
    })
});