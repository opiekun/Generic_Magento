define([
    'jquery',
    'uiRegistry',
    'Magento_Ui/js/form/element/single-checkbox'
], function ($, registry, Checkbox) {
    'use strict';

    return Checkbox.extend({
        defaults: {
            modules: {
                parentComponent: '${ $.parentName }'
            }
        },

        /**
         * @inheritdoc
         */
        onCheckedChanged: function (newChecked) {
            this._super();
            var self    = this;
            var elems   = this.parentComponent().elems();
            var visible = newChecked ? true : false;

            elems.each(function (elem) {
                if (elem.index.indexOf('_top') !== -1 || elem.index.indexOf('_right') !== -1
                    || elem.index.indexOf('_bottom') !== -1 || elem.index.indexOf('_left') !== -1) {
                    if (newChecked && (elem.displayArea == 'margin' || elem.displayArea == 'border' || elem.displayArea == 'padding')) {
                        if (elem.index.indexOf('_left') !== -1) {
                            elem.visible(visible);
                            self.onValueChanged(elem.displayArea, elem.value());
                        } else {
                            elem.visible(!visible);
                        }
                    } else {
                        elem.visible(true);
                    }
                }
            });
        },

        /**
         * @inheritdoc
         */
        onMarginChanged: function(value) {
            this.onValueChanged('margin', value);
        },

        /**
         * @inheritdoc
         */
        onBorderChanged: function(value) {
            this.onValueChanged('border', value);
        },

        /**
         * @inheritdoc
         */
        onPaddingChanged: function(value) {
            this.onValueChanged('padding', value);
        },

        /**
         * @inheritdoc
         */
        onValueChanged: function(type, value) {
            if (parseInt(this.value())) {
                var elems = this.parentComponent().elems();
                elems.each(function (elem) {
                    if (elem.displayArea == type && elem.index.indexOf(type) !== -1) {
                        elem.value(value);
                    }
                });
            }
        },

        /**
         * Get true/false key from valueMap by value.
         *
         * @param {*} value
         * @returns {Boolean|undefined}
         */
        getReverseValueMap: function getReverseValueMap(value) {
            var bool = false;

            _.some(this.valueMap, function (iValue, iBool) {
                if (iValue == value) {
                    bool = iBool === 'true';

                    return true;
                }
            });

            return bool;
        }
    });
});