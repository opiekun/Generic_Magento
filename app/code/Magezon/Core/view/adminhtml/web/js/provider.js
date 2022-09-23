define([
    'jquery',
    'Magento_Ui/js/grid/provider'
], function ($, Provider) {
    'use strict';

    return Provider.extend({

        initObservable: function () {
            this._super()
            .observe('loading');
            return this;
        },

        /**
         * Initializes regular properties of instance.
         *
         * @returns {Abstract} Chainable.
         */
        initConfig: function (config) {

            config['update_url'] = window.mgzReportsConfig.update_url;

            this._super(config);

            return this;
        },

        initLoadData: function () {
            this.setData({
                items: [],
                totalRecords: window.mgzReportsConfig.totalRecords
            });
        },

        /**
         * Reloads data with current parameters.
         *
         * @returns {Promise} Reload promise object.
         */
        reload: function (options) {
            this.loading(true);
            options = options || {};
            options['refresh'] = true;
            var request = this.storage().getData(this.params, options);

            this.trigger('reload');

            request
                .done(this.onReload)
                .fail(this.onError.bind(this));

            return request;
        },

        /**
         * Handles successful data reload.
         *
         * @param {Object} data - Retrieved data object.
         */
        onReload: function (data) {
            this._super(data);
            this.loading(false);
        }
    });
});
