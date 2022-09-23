define([
    'jquery',
    'IWD_AddressValidation/js/validation',
    'jquery/ui'
],

function ($, iwdAddressValidation){
    'use strict';

    $.widget('mage.iwdAddressValidationMultishipping', $.mage.iwdAddressValidation, {
        options: {
            urlValidation: "",
            allowInvalidAddress: true,
            formId: 'form.form-address-edit',
            nextStepButtonId: "button.save",
            validateAddressTimeout:0,
            addressMap: {
                'street':  'street[]',
                'city':    'city',
                'country_id': 'country_id',
                'postcode':   'postcode',
                'region_id':  'region_id',
                'region':     'region'
            },
            address: {}
        },
        request: null,
        currentModal: null,
        validation: false,

        init: function(options){
            this._initOptions(options);

            this.checkCandidateAddress();
            this.onAddressForm();
            this.onClickNextButton();
        },

        onAddressForm: function(){
            var self = this;
            var form_inputs = this.options.formId + ' input, ' + this.options.formId + ' select';
            var map = self.toArray(self.options.addressMap);

            $(document).on('change', form_inputs, (function() {
                if (map.indexOf($(this).attr('name')) !== -1){
                    clearTimeout(self.validateAddressTimeout);
                    self.validateAddressTimeout = setTimeout(function(){
                        self.readAddressForm();
                        self.validateAddress();
                    }, 500);
                }
            }));
        },

        onClickNextButton: function() {
            var self = this;
            $(document).on('click touchstart', this.options.nextStepButtonId, (function(e) {
                if(self.validation == true){
                    $(self.options.nextStepButtonId).attr('disabled', null);
                    return;
                }

                e.preventDefault();
                $(self.options.nextStepButtonId).attr('disabled', 'disabled');

                if(self.validation == false) {
                    self.readAddressForm();
                    self.validateAddress();
                }
            }));
        },

        readAddressForm: function(){
            var address = {};
            var formId = this.options.formId;

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

            var street = "";
            $.each( $(this.options.formId + ' [name="street[]"]'), function(){
                street += ' ' + $(this).val();
            });
            address['street'] = street.trim();

            return this.options.address = address;
        },

        updateAddress:function(response){
            if ($("input[name='candidate']").length == 0){
                return true;
            }

            var checkedAddress = $("input[name='candidate']:checked");
            if (checkedAddress.length == 0){
                return false;
            }

            if (checkedAddress.val() == 'origin'){
                this.whenAddressValid();
                return true;
            }

            var address = response.suggested_addresses[checkedAddress.val()];
            this.updateFormAddress(address);

            return true;
        },

        updateFormAddress:function(address){
            var formId = this.options.formId;
            var map = this.options.addressMap;

            delete map.street;

            $.each($(formId + ' [name^="street"]'), function(){ $(this).val(''); });
            $($(formId + ' [name="street[]"]')[0]).val(address['street']).trigger('change');

            $.each(map, function(i, e){
                var elem = $(formId + ' [name="' + e + '"]');
                if (elem && elem.length > 0 && address[i] && address[i]!=''){
                    elem.val(address[i]).trigger('change');
                }
            });
        }
    });

    return $.mage.iwdAddressValidationMultishipping;
});