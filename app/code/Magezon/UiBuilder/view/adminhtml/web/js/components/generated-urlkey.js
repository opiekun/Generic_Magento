/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Catalog/js/components/import-handler',
    'underscore',
    'uiRegistry'
], function (Element, _, registry) {
    'use strict';

    return Element.extend({

        /**
         * Update field with mask value, if it's allowed.
         *
         * @param {Object} placeholder
         * @param {Object} component
         */
        updateValue: function (placeholder, component) {
            var string = this.mask || '',
                nonEmptyValueFlag = false;

            if (placeholder) {
                this.values[placeholder] = component.getPreview() || '';
            }

            if (!this.allowImport) {
                return;
            }

            _.each(this.values, function (propertyValue, propertyName) {
                string = string.replace('{{' + propertyName + '}}', propertyValue);
                nonEmptyValueFlag = nonEmptyValueFlag || !!propertyValue;
            });

            if (nonEmptyValueFlag) {
                string = string.replace(/(<([^>]+)>)/ig, ''); // Remove html tags
                string = string.replace(/ /g, '-');
                string = string.toLowerCase();
                this.value(string);
            } else {
                this.value('');
            }
        }
    });
});
