define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'mage/translate',
    'jquery/ui'
],

function ($, modal, $t){
    'use strict';

    $.widget('mage.iwdAddressValidationCheckout', {
        options: {
            urlValidation: "",
            allowInvalidAddress: true,

            formBillingId: '#order-billing_address',
            formShippingId: '#order-shipping_address',

            existingBillingAddress: "#order-billing_address_customer_address_id",
            existingShippingAddress: "#order-shipping_address_customer_address_id",

            nextStepButtonId: "button#submit_order_top_button, button.save",

            validateAddressTimeout:0,

            addressMapShipping: {
                'street':  'order[shipping_address][street][0]',
                'street1': 'order[shipping_address][street][1]',
                'street2': 'order[shipping_address][street][2]',
                'street3': 'order[shipping_address][street][3]',
                'city':    'order[shipping_address][city]',
                'country_id': 'order[shipping_address][country_id]',
                'postcode':   'order[shipping_address][postcode]',
                'region_id':  'order[shipping_address][region_id]',
                'region':     'order[shipping_address][region]'
            },
            addressMapBilling: {
                'street':  'order[billing_address][street][0]',
                'street1': 'order[billing_address][street][1]',
                'street2': 'order[billing_address][street][2]',
                'street3': 'order[billing_address][street][3]',
                'city':    'order[billing_address][city]',
                'country_id': 'order[billing_address][country_id]',
                'postcode':   'order[billing_address][postcode]',
                'region_id':  'order[billing_address][region_id]',
                'region':     'order[billing_address][region]'
            },

            address: []
        },
        request: null,
        validationBilling: false,
        validationShipping: false,
        nextButtonHasBeenClicked: false,

        init: function(options){
            this.initOptions(options);

            this.changeAddressForm(this.options.formBillingId);
            this.changeAddressForm(this.options.formShippingId);

            this.changeExistingAddress(this.options.existingBillingAddress, this.options.formBillingId);
            this.changeExistingAddress(this.options.existingShippingAddress, this.options.formShippingId);

            var self = this;
            this.onClickNextButton();
            document.addEventListener('totalsBlockReInit', function (e) {
                self.onClickNextButton();
            }, false);

        },

        initOptions:function(options){
            var self = this;

            options = options || {};
            $.each(options, function(i, e){self.options[i] = e;});
        },

        onClickNextButton: function() {
            var self = this;
            $(this.options.nextStepButtonId).attr('onclick', null);

            $(document).off('click touchstart', this.options.nextStepButtonId);
            $(document).on('click touchstart', this.options.nextStepButtonId, (function(e) {
                self.nextButtonHasBeenClicked = true;
                if(self.validationBilling == true && self.validationShipping == true){
                    $(self.options.nextStepButtonId).attr('disabled', null);
                    $(self.options.nextStepButtonId).attr('onclick', 'order.submit()');
                    return;
                }

                $(self.options.nextStepButtonId).attr('onclick', null);
                e.preventDefault();

                $(self.options.nextStepButtonId).attr('disabled', 'disabled');

                if(self.validationShipping == false){
                    self.firstValidation(self.options.formShippingId);
                }
                if(self.validationBilling == false){
                    self.firstValidation(self.options.formBillingId);
                }
            }));
        },

        changeAddressForm: function(formId){
            var self = this;
            var form_inputs = formId + ' input, ' + formId + ' select';
            $(document).on('change', form_inputs, (function() {

                if(formId == self.options.formBillingId){
                    self.validationBilling = false;
                }
                if(formId == self.options.formShippingId){
                    self.validationShipping = false;
                }

                if($('#order-shipping_same_as_billing:checked').length == 1 &&
                    formId == self.options.formShippingId
                ){
                    return;
                }

                var map = self.toArray(self.getAddressMap(formId));
                if (map.indexOf($(this).attr('name')) !== -1){
                    clearTimeout(self.validateAddressTimeout);
                    self.validateAddressTimeout = setTimeout(function(){
                        self.readAddressForm(formId);
                        self.validateAddress(formId);
                    }, 500);
                }
            }));
        },

        changeExistingAddress:function(selectId, formId) {
            var self = this;
            $(document).on('change', selectId, function() {
                clearTimeout(self.validateAddressTimeout);
                self.validateAddressTimeout = setTimeout(function () {
                    self.readAddressForm(formId);
                    self.validateAddress(formId);
                }, 50);
            });
        },

        firstValidation: function(formId) {
            if($('#order-shipping_same_as_billing:checked').length == 1 &&
                formId == this.options.formShippingId
            ) {
                return;
            }

            this.readAddressForm(formId);
            this.validateAddress(formId);
        },

        getAddressMap:function(formId){
            if(formId == this.options.formBillingId){
                return this.options.addressMapBilling;
            }
            return this.options.addressMapShipping;
        },

        toArray:function(obj){
            var dataArray = [];
            for(var o in obj) {
                dataArray.push(obj[o]);
            }
            return dataArray;
        },

        readAddressForm: function(formId){
            var address = {};
            var map = this.getAddressMap(formId);

            address['street'] = '';
            $.each(map, function(i, e){
                var elem = $(formId + ' [name="' + e + '"]');
                if (elem && elem.length > 0){
                    if(i.indexOf('street') !== -1){
                        address['street'] += ' ' + elem.val();
                    } else {
                        address[i] = elem.val();
                    }
                }
            });

            return this.options.address[formId] = address;
        },

        validateAddress: function(formId){
            if (this.checkIsAddressFilled(formId)){
                this.checkIsAddressValid(formId);
            } else {
                if(self.validationBilling == true && self.validationShipping == true){
                    order.submit();
                }
            }
        },

        checkIsAddressFilled: function(formId){
            var empty = [];

            $.each(this.options.address[formId], function(i, e) {
                if (!e || e.length == 0 || e == 0){
                    empty.push(i);
                }
            });

            if (empty.length == 0){
                return true;
            }

            if (empty.length > 2){
                return false;
            }

            if (empty.length > 1){
                return (empty.indexOf('region_id') != -1 && empty.indexOf('region') != -1);
            }

            return !(empty.indexOf('region_id') == -1) || !(empty.indexOf('region') == -1);
        },

        checkIsAddressValid:function(formId){
            var self = this;
            if(this.request && this.request.readystate != 4){
                this.request.abort();
            }

            var data = this.options.address[formId];
            data['form_key'] = FORM_KEY;

            this.request = $.ajax({
                url: this.options.urlValidation,
                data: data,
                type: 'post',
                dataType: 'json',
                context: this,
                beforeSend: function() {
                    self.disableNextButton(self.options.content.validatingAddress);
                },
                complete: function() {

                }
            })
                .done(function(response) {
                    if (response.error){
                        console.log(JSON.stringify(response));
                    }

                    if(response.is_valid){
                        self._isAddressValid();
                        self.updateIsValidated(formId);
                    } else {
                        self._isAddressInvalid(response, formId);
                    }

                    this.nextButtonHasBeenClicked = false;
                })
                .fail(function(error) {
                    console.log(JSON.stringify(error));
                });
        },

        _isAddressValid:function(){
            $('.iwd-address-validation-error-message').remove();
            $(this.options.nextStepButtonId).attr('disabled', null);
            $(this.options.nextStepButtonId).attr('onclick', 'order.submit()');
            if(this.nextButtonHasBeenClicked == true){
                order.submit();
            }
        },

        _isAddressInvalid:function(response, formId){
            this.disableNextButton(this.options.content.updateAddress);
            this._showModal(response, formId);
        },

        _showModal:function (response, formId) {
            var self = this;

            $('.iwd-address-validation-popup').removeClass('_show').addClass('_hide');

            modal({
                title: $t(self.options.content.header),
                content: response.modal_content,
                modalClass: "iwd-address-validation-popup",
                buttons:[
                    {
                        text: $t('Continue'),
                        class: '',
                        click: function() {
                            $(self.options.nextStepButtonId).attr('disabled', 'disabled');

                            if (self.updateAddress(response, formId)){
                                this.closeModal();
                            } else {
                                $('.iwd-address-validation-popup .modal-content .mage-error').remove();
                                $('.iwd-address-validation-popup .modal-content')
                                    .append('<div generated="true" class="mage-error">' +
                                    $t(self.options.content.makeChoice) +
                                    '</div>');
                            }
                        }
                    }
                ]
            })
        },

        updateAddress:function(response, formId){
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
            this.updateFormAddress(address, formId);

            return true;
        },

        updateIsValidated: function(formId){
            if(formId == this.options.formBillingId){
                this.validationBilling = true;
            }
            if(formId == this.options.formShippingId){
                this.validationShipping = true;
            }
        },

        updateFormAddress:function(address, formId)
        {
            this.updateIsValidated(formId);

            var map = this.getAddressMap(formId);

            $.each($(formId + ' [name^="street"]'), function(){ $(this).val(''); });

            $.each(map, function(i, e){
                var elem = $(formId + ' [name="' + e + '"]');
                if (elem && elem.length > 0 && address[i] && address[i]!=''){
                    elem.val(address[i]).trigger('change');
                } else {
                    if (elem && elem.length) {
                        elem.val('').trigger('change');
                    }
                }
            });

            if($('#order-shipping_same_as_billing:checked').length == 1 &&
                formId == this.options.formBillingId
            ){
                order.setShippingAsBilling(1);
            }
        },

        disableNextButton: function(message){
            $(this.options.nextStepButtonId).attr('disabled', 'disabled');
            $('.iwd-address-validation-error-message').remove();
            $('button.save').parent()
                .append('<div style="clear:both"></div><label generated="true" class="iwd-address-validation-error-message mage-error">' +
                $t(message) +
                '</label>');
        }
    });

    return $.mage.iwdAddressValidationCheckout;
});