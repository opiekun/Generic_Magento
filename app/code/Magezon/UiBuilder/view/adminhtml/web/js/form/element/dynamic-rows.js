define([
	'jquery',
    'Magento_Ui/js/dynamic-rows/dynamic-rows'
], function ($, dynamicRows) {
    'use strict';

    return dynamicRows.extend({
        defaults: {
            template: 'Magezon_UiBuilder/dynamic-rows/templates/default'
        },

        /**
         * Reset data
         */
    	resetRecordData: function() {
            this.recordData([]);
            this.clear();
            this.showSpinner(false);
        },

        /**
         * Update options by current value
         */
        updateRecordData: function(recordData) {
            this.recordData(recordData);
            this.reinitRecordData();
            this.initChildren();
        },

        /**
         * Reinit record data in order to remove deleted values
         *
         * @return void
         */
        reinitRecordData: function () {
            this.recordData(
                _.filter(this.recordData(), function (elem) {
                    return elem && elem[this.deleteProperty] !== this.deleteValue;
                }, this)
            );
        },

        /**
         * Get record count with filtered delete property.
         *
         * @returns {Number} count
         */
        getRecordCount: function () {
            return _.filter(this.recordData(), function (record) {
                return record && record[this.deleteProperty] !== this.deleteValue;
            }, this).length;
        },

        /**
         * Set classes
         *
         * @param {Object} data
         *
         * @returns {Object} Classes
         */
        setClasses: function (data) {
            var additional;

            if (_.isString(data.additionalClasses)) {
                additional = data.additionalClasses.split(' ');
                data.additionalClasses = {};

                additional.forEach(function (name) {
                    data.additionalClasses[name] = true;
                });
            }

            if (!data.additionalClasses) {
                data.additionalClasses = {};
            }

            if (data.index) {
                data.additionalClasses['col-' + data.index] = true;
            } else {
                data.additionalClasses['col-' + data.name] = true;
            }

            _.extend(data.additionalClasses, {
                '_fit': data.fit,
                '_required': data.required,
                '_error': data.error,
                '_empty': !this.elems().length,
                '_no-header': this.columnsHeaderAfterRender || this.collapsibleHeader,
            });

            return data.additionalClasses;
        }

    });
});