define([
    'jquery',
    'uiClass',
    "PluginCompany_ProductPdf/js/lib/tinycolor",
    "jquery/colorpicker/js/colorpicker",
], function ($, Class, tinycolor) {
    'use strict';

    return Class.extend({
        el: '',
        value: '',
        elementData: {},
        scrollToTopAfterSubmit: true,
        initialize: function (config, node) {
            this.initConfig(config);
            this.el = $(node);
            this.initValue();
            this.initColorPicker();
        },
        initValue: function() {
            this.value = this.elementData.value;
        },
        initColorPicker: function() {
            var self = this;
            this.el.ColorPicker({
                color: self.value,
                onChange: function(hsb, hex, rgb){
                    self.el.css("backgroundColor", "#" + hex).val(hex);
                    self.value = hex;
                    self.switchTextColor();
                }
            })
            self.el.css("backgroundColor", "#" + self.value);
            self.switchTextColor();
        },
        switchTextColor: function() {
            if(tinycolor('#' + this.value).isDark()){
                this.el.css('color', '#fff');
            }else{
                this.el.css('color', '#000');
            }
        }
    });

});