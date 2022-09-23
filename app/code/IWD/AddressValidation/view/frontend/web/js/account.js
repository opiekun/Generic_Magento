//TODO: it does not ready yet

define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'mage/translate',
    'jquery/ui'
],
function ($, modal, $t){
    'use strict';

    $.widget('mage.iwdAddressValidationAccount', {
        options: {
            urlValidation: "",
            allowInvalidAddress: true,
            formId: '#form-validate',
            nextStepButtonId: "#form-validate button.save",
            shipHereButton: "button.action-select-shipping-item",
            newShippingAddressForm: '.opc-new-shipping-address',

            validateAddressTimeout:0,
            addressMap: ['street[]', 'city', 'country_id', 'postcode', 'region_id', 'region'],
            address: {}
        },
        request: null,
        currentModal: null,

        init: function(options){
            this._initOptions(options);
            this.checkCandidateAddress();

            this._onAddressForm();

            this.readAddressForm();
            this._validateAddress();
        },

        _initOptions:function(options){
            var self = this;

            options = options || {};
            $.each(options, function(i, e){self.options[i] = e;});
        },

        _onAddressForm: function(){
            var self = this;
            var form_inputs = this.options.formId + ' input, ' + this.options.formId + ' select';
            $(document).on('change', form_inputs, (function() {
                if (self.options.addressMap.indexOf($(this).attr('name')) !== -1){
                    clearTimeout(self.validateAddressTimeout);
                    self.validateAddressTimeout = setTimeout(function(){
                        self.readAddressForm();
                        self._validateAddress();
                    }, 500);
                }
            }));
        },

        readAddressForm: function(){
            var formId = this.options.formId;
            var address = {};

            address['street'] = "";
            $.each( $(formId + ' [name="street[]"]'), function(){
                address['street'] += ' ' + $(this).val();
            });

            $.each(['city', 'country_id', 'postcode', 'region', 'region_id'], function(i, e){
                var elem = $(formId + ' [name="' + e + '"]');
                if (elem && elem.length > 0){
                    address[e] = elem.val();
                }
            });

            return this.options.address = address;
        },

        _validateAddress: function(){
            if (this._checkIsAddressFilled()){
                this._checkIsAddressValid();
            }
        },

        _checkIsAddressFilled: function(){
            var empty = [];
            $.each(this.options.address, function(i, e) {
                if (!e || e.length == 0 || e == 0){
                    empty.push(i);
                }
            });

            if (empty.length == 0){
                return true;
            }

            if (empty.length > 1){
                return (empty.indexOf('region_id') != -1 && empty.indexOf('region') != -1);
            }

            return !(empty.indexOf('region_id') == -1) || !(empty.indexOf('region') == -1);
        },

        _checkIsAddressValid:function(){
            var self = this;

            if(this.request && this.request.readystate != 4){
                this.request.abort();
            }

            this.request = $.ajax({
                url: this.options.urlValidation,
                data: this.options.address,
                type: 'post',
                dataType: 'json',
                context: this,
                beforeSend: function() {
                    $(self.options.nextStepButtonId).attr('disabled', 'disabled');
                    $('.iwd-address-validation-error-message').remove();
                    $(self.options.nextStepButtonId).parent().append('<div style="clear:both"></div><div class="iwd-address-validation-error-message mage-error" generated="true">Please, update shipping address before continue.</div>')
                },
                complete: function() {

                }
            })
            .done(function(response) {
                if (response.error){
                    console.log(JSON.stringify(response));
                } else {
                    if(response.is_valid){
                        this._isAddressValid();
                    } else {
                        this._isAddressInvalid(response);
                    }
                }
            })
            .fail(function(error) {
                console.log(JSON.stringify(error));
            });
        },

        _isAddressValid:function(){
            $('.iwd-address-validation-error-message').remove();
            $(this.options.nextStepButtonId).attr('disabled', null);
        },

        _isAddressInvalid:function(response){
            this._showModal(response);
        },

        _showModal:function (response) {
            var self = this;

            $('.iwd-address-validation-popup').removeClass('_show').addClass('_hide');

            modal({
                title: $t('Address Validation'),
                content: response.modal_content,
                modalClass: "iwd-address-validation-popup",
                buttons:[
                    {
                        text: $t('Continue'),
                        class: '',
                        click: function() {
                            if (self._updateAddress(response)){
                                this.closeModal();
                            }
                        }
                    }
                ]
            })
        },

        _updateAddress:function(response){
            if ($("input[name='candidate']").length == 0){
                return true;
            }

            var checkedAddress = $("input[name='candidate']:checked");
            if (checkedAddress.length == 0){
                return false;
            }

            if (checkedAddress.val() == 'origin'){
                this._isAddressValid();
                return true;
            }

            var address = response.suggested_addresses[checkedAddress.val()];
            this.updateFormAddress(address);

            return true;
        },

        updateFormAddress:function(address)
        {
            var formId = this.options.formId;

            $.each(['city', 'country_id', 'postcode', 'region', 'region_id'], function(i, e){
                var elem = $(formId + ' [name="' + e + '"]');
                if (elem && elem.length > 0 && address[e] && address[e]!=''){
                    elem.val(address[e]).trigger('change');
                }
            });

            $.each( $(formId + ' [name="street[]"]'), function(){ $(this).val(''); });
            $($(formId + ' [name="street[]"]')[0]).val(address['street']);
        }
    });

    return $.mage.iwdAddressValidationAccount;
});