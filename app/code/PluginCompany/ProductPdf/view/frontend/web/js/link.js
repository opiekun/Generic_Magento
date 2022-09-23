define([
    'uiClass',
    'jquery',
    'underscore'
], function(Class, $, _) {
    return Class.extend({
        childUrls: [],
        mainUrl: '',
        node: null,
        initialize: function (config, node) {
            this._super();
            $(node)[config.insertMethod](config.linkSelector);

            this.childUrls = config.childProductUrls;
            this.mainUrl = this.mainProductUrl;
            this.node = $(node);
            this.optionsMap = config.optionsMap;

            if(config.childLinkUrlEnabled) {
                this.attachConfigurableProductEventHandler();
            }
        },
        attachConfigurableProductEventHandler: function() {
            var self = this;
            $(".product-options-wrapper div" ).click(function() {
                self.setPdfLinkUrl();
            });
            $(".product-options-wrapper select, .product-options-wrapper input" ).change(function() {
                self.setPdfLinkUrl();
            });
        },
        setPdfLinkUrl: function() {
            this.node.prop('href', this.getSelectedUrl());
        },
        getSelectedUrl: function() {
            var selectedOptions = this.getSelectedConfigurableOptions();

            if(!selectedOptions) return this.mainUrl;

            var found_id = _.findKey(this.optionsMap, selectedOptions);
            if(found_id && typeof this.childUrls[found_id] != 'undefined') {
                return this.childUrls[found_id];
            }
            return this.mainUrl;
        },
        getSelectedConfigurableOptions: function(){
            if(!jQuery('#product_addtocart_form [name^=super_attribute]').length) {
                return false;
            }
            var selectedOptions = {};
            jQuery('#product_addtocart_form [name^=super_attribute]').each(function(){
                var attributeId = jQuery(this).attr('name').replace(/\D/g,'');
                var optionId = jQuery(this).val();
                selectedOptions[attributeId] = optionId.toString();
            });
            return selectedOptions;
        }
    });
});