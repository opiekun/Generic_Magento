define([
        'jquery',
        'IWD_AddressValidation/js/validation',
        'Magento_Ui/js/modal/alert',
        'mage/translate',
        'Magento_Checkout/js/model/quote',
        'jquery/ui'
    ],

    function ($, iwdAddressValidation, modal, $t, quote){
        'use strict';

        $.widget('mage.iwdAddressValidationCheckout', $.mage.iwdAddressValidation, {
            options: {
                urlValidation: "",
                allowInvalidAddress: true,
                formId: '#co-shipping-form',
                nextStepButtonId: "button.continue, button.action-save-address",
                closePopup: '.action-close, .action-hide-popup',
                shipHereButton: "button.action-select-shipping-item",
                newShippingAddressForm: '.opc-new-shipping-address',

                validateAddressTimeout:0,
                address: {},

                addressMap: {
                    'street':  'street[0]',
                    'street1': 'street[1]',
                    'street2': 'street[2]',
                    'street3': 'street[3]',
                    'city':    'city',
                    'country_id': 'country_id',
                    'postcode':   'postcode',
                    'region_id':  'region_id',
                    'region':     'region'
                }
            },
            request: null,
            currentModal: null,
            isExistingAddress: true,
            validation: false,

            init: function(options){
                this._initOptions(options);

                this.checkCandidateAddress();

                this.onClickNextButton();
                this.onClickCancelButton();

                this.onAddressForm();
                this.selectExistingAddress();
            },



            onClickNextButton: function() {
                var self = this;
                $(document).on('click touchstart', this.options.nextStepButtonId, (function(e) {
                    if(self.validation == true){
                        $(self.options.nextStepButtonId).attr('disabled', null);
                        return;
                    }

                    if(self.validation == false) {
                        e.preventDefault();
                        $(self.options.nextStepButtonId).attr('disabled', 'disabled');
                        self.readAddressQuote();
                        self.validateAddress();
                    } else {
                        $(self.options.nextStepButtonId).attr('disabled', null);
                    }
                }));
            },

            onClickCancelButton: function(){
                var self = this;
                $(document).on('click touchstart', this.options.closePopup, (function(e) {
                    self.validation = false;
                    self.disableNextButton('Please, recheck and update address before continue.');
                }));
            },

            onAddressForm: function(){
                var self = this;
                var form_inputs = this.options.formId + ' input, ' + this.options.formId + ' select';
                var map = self.toArray(self.options.addressMap);

                $(document).on('change', form_inputs, (function(){
                    self.checkIsExistingAddress();

                    if (!self.isExistingAddress && map.indexOf($(this).attr('name')) !== -1){
                        self.validation = 'changed';
                        clearTimeout(self.validateAddressTimeout);
                        self.validateAddressTimeout = setTimeout(function(){
                            self.isExistingAddress = false;
                            self.readAddressForm();
                            self.validateAddress();
                        }, 500);
                    }
                }));
            },

            selectExistingAddress:function(){
                var self = this;
                $(document).on('click touchstart', this.options.shipHereButton, (function() {
                    clearTimeout(self.validateAddressTimeout);
                    self.validateAddressTimeout = setTimeout(function(){
                        self.isExistingAddress = true;
                        self.validation = 'changed';
                        self.readAddressQuote();
                        self.validateAddress();
                    }, 500);
                }));
            },

            readAddressQuote: function(){
                var addressQuote = quote.shippingAddress();
                var address = {};

                address['postcode'] = addressQuote.postcode;
                address['city'] = addressQuote.city;
                address['country_id'] = addressQuote.countryId;
                address['region'] = addressQuote.region;
                address['region_id'] = addressQuote.regionId;

                var street = '';
                $.each(addressQuote.street, function(i, e){
                    street += ' ' + e;
                });
                address['street'] = street.trim();

                return this.options.address = address;
            },

            readAddressForm: function(){
                var address = {};
                var formId = this.options.formId;

                address['street'] = '';
                $.each(this.options.addressMap, function(i, e){
                    var elem = $(formId + ' [name="' + e + '"]');
                    if (elem && elem.length > 0){
                        if(i.indexOf('street') !== -1){
                            address['street'] += ' ' + elem.val();
                        } else {
                            address[i] = elem.val();
                        }
                    }
                });

                return this.options.address = address;
            },

            checkIsExistingAddress:function(){
                this.isExistingAddress =
                    $(".shipping-address-items").length == 1 &&
                    $('.modal-popup._show #co-shipping-form').length == 0;

                return this.isExistingAddress;
            },

            updateExistingAddress:function(address){
                var self = this;
                $('button.action-show-popup').trigger('click');
                setTimeout(function(){
                    self.validation = 'existing';
                    self.updateFormAddress(address);
                }, 50);
            },

            updateAddress: function(response){
                this.checkIsExistingAddress();
                var self = this;
                if ($("input[name='candidate']").length == 0){
                    if(this.isExistingAddress){
                        this.updateExistingAddress(response.original_address);
                    }
                    self.hideOverlay();
                    return true;
                }

                var checkedAddress = $("input[name='candidate']:checked");
                if (checkedAddress.length == 0){
                    self.hideOverlay();
                    return false;
                }

                if (checkedAddress.val() == 'origin'){
                    this.whenAddressValid();
                    self.hideOverlay();
                    return true;
                }

                var address = response.suggested_addresses[checkedAddress.val()];

                if(this.isExistingAddress){
                    this.updateExistingAddress(address);
                } else {
                    this.updateFormAddress(address);
                }
                self.hideOverlay();
                return true;
            },

            hideOverlay: function(){
                var overlay = jQuery('.modals-overlay');
                if (overlay.length != 0 && jQuery(overlay[0]).attr("style") == 'z-index: 900;'){
                    overlay.attr("style", "z-index: 899;");
                }
            }
        });

        return $.mage.iwdAddressValidationCheckout;
    });