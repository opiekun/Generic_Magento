define([
	'jquery',
    'Magento_Ui/js/form/element/single-checkbox'
], function ($, SingleCheckbox) {
    'use strict';

    return SingleCheckbox.extend({

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