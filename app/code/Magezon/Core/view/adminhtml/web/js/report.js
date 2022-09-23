define([
    'mage/translate',
    'uiComponent'
], function ($t, Component) {
    'use strict';

    return Component.extend({

    	defaults: {
    		template: 'Magezon_Core/report'
    	},

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .track({
                    products: []
                });
            return this;
        },
    })
});