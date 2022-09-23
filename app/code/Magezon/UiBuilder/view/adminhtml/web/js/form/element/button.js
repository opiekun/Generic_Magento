define([
    'Magento_Ui/js/form/components/button'
], function (Button) {
    'use strict';

    return Button.extend({
    	defaults: {
            buttonClasses: {},
            elementTmpl: 'Magezon_UiBuilder/form/element/button'
        },
    })
});