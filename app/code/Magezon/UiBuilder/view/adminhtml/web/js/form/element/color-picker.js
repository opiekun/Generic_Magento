define([
	'jquery',
    'underscore',
    './abstract',
    'Magezon_Core/js/jquery.minicolors'
], function ($, _, Abstract) {
    'use strict';

    return Abstract.extend({

        defaults: {
            elementTmpl: 'Magezon_UiBuilder/form/element/color'
        },

        onElementRender: function () {
    		this.loadColorPicker();
        },

        afterLoadData: function () {
            this.loadColorPicker();
        },

        beforeLoadData: function () {
            this.destroyColorPicker();
        },

        loadColorPicker: function () {
            var config = {
                theme: 'bootstrap',
                keywords: 'transparent, initial, inherit'
            };
            _.extend(config, this.colorPicerConfig)
            $('#' + this.uid).minicolors(config);
        },

        destroyColorPicker: function () {
            $('#' + this.uid).minicolors('destroy');
        }
    });
});