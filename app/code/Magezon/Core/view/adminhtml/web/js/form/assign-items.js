/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* global $, $H */

define([
    'jquery',
    'mage/adminhtml/grid'
], function (jquery) {
    'use strict';

    return function (config) {
        var selectedItems = $H(config.selectedItems),
        gridJsObject      = window[config.gridJsObjectName],
        selector          = config.selector,
        tabIndex          = 1000;

        $(selector).value = Object.toJSON(selectedItems);

        /**
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerRow(grid, element, checked) {
            if (checked) {
                if (element.positionElement) {
                    element.positionElement.disabled = false;
                    selectedItems.set(element.value, element.positionElement.value);
                }
            } else {
                if (element.positionElement) {
                    element.positionElement.disabled = true;
                }
                selectedItems.unset(element.value);
            }
            $(selector).value = Object.toJSON(selectedItems);
            var items = {};
            items[config.ajaxParam + '[]'] = selectedItems.keys();
            grid.reloadParams = items;
        }

        /**
         * Click on product row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function rowClick(grid, event) {
            var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;

            if (trElement) {
                checkbox = Element.getElementsBySelector(trElement, 'input');

                if (checkbox[0]) {
                    checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                }
            }
        }

        /**
         * Change product position
         *
         * @param {String} event
         */
        function positionChange(event) {
            var element = Event.element(event);

            if (element && element.checkboxElement && element.checkboxElement.checked) {
                selectedItems.set(element.checkboxElement.value, element.value);
                $(selector).value = Object.toJSON(selectedItems);
            }
        }

        /**
         * @param {Object} grid
         * @param {String} row
         */
        function rowInit(grid, row) {
            var checkbox = $(row).getElementsByClassName('checkbox')[0],
                position = $(row).getElementsByClassName('input-text')[0];

                var arr = Object.values(selectedItems)[0];
                for (var k in arr) {
                    if (k == checkbox.value) {
                        $(position).value = arr[k];
                        gridJsObject.setCheckboxChecked(checkbox, true);
                    }
                }

            if (checkbox && position) {
                checkbox.positionElement = position;
                position.checkboxElement = checkbox;
                position.disabled = !checkbox.checked;
                position.tabIndex = tabIndex++;
                Event.observe(position, 'keyup', positionChange);
            }
        }

        gridJsObject.rowClickCallback = rowClick;
        gridJsObject.initRowCallback = rowInit;
        gridJsObject.checkboxCheckCallback = registerRow;

        if (gridJsObject.rows) {
            gridJsObject.rows.each(function (row) {
                rowInit(gridJsObject, row);
            });
        }
    };
});
