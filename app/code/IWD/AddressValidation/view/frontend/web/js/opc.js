define([
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/full-screen-loader',
        'jquery/ui'
    ],

    function ($, quote, fullScreenLoader) {
        'use strict';

        $.widget('mage.iwdAddressValidationCheckout', {
            options: {
                urlValidation: "",
                allowInvalidAddress: true,
                formId: '#co-shipping-form',
                regionSelector: '#co-shipping-form div[name="shippingAddress.region_id"]',
                validateAddressTimeout: 0,
                address: {},
                telephone:null,
                addressMap: {
                    'street': 'street[0]',
                    'street1': 'street[1]',
                    'street2': 'street[2]',
                    'street3': 'street[3]',
                    'city': 'city',
                    'country_id': 'country_id',
                    'postcode': 'postcode',
                    'region_id': 'region_id',
                    'region': 'region'
                },
                selectHeight: '50px'
            },
            request: null,

            init: function (options) {
                window.iwdAddressValidationIsValid = false;
                this._initOptions(options);
                this.onCheckCandidateAddress();
                this.onFillAddressForm();
                this.onChangeAddressList();
                this.onButtonsClick();
            },

            _initOptions: function (options) {
                var self = this;
                options = options || {};
                $.each(options, function (i, e) {
                    self.options[i] = e;
                });
            },

            validateAddress: function () {
                if (!quote.isVirtual() && this.checkIsAddressFilled()) {
                    this.checkIsAddressValid();
                }
            },

            onButtonsClick: function () {
                var self = this;
                $(document).on('click', '#iwd_address_validation_continue_button', function () {
                    if ($('#shipping_address_id').length) {
                        $('#shipping_address_id').val('').trigger('change', true);
                    }

                    if ($('#iwd_opc_address_validation_select').val()) {
                        self.useSuggestedAddress($('#iwd_opc_address_validation_select').val());
                    } else {
                        window.iwdAddressValidationIsValid = self.allowInvalidAddress;
                        self.closeModal();
                    }
                });

                $(document).on('click', '#iwd_address_validation_origin_address_button', function () {
                    self.closeModal();
                    window.iwdAddressValidationIsValid = true;
                });
            },

            updateFormAddress: function (address) {
                var formId = this.options.formId;
                var map = this.options.addressMap;

                $(formId + ' [name^="street"]').eq(0).val('');

                $.each(map, function (i, e) {
                    var elem = $(formId + ' [name="' + e + '"]');
                    if (elem && elem.length > 0 && address[i] && address[i] !== '') {
                        elem.val(address[i]).trigger('change', true);
                    } else {
                        if (elem && elem.length) {
                            elem.val('').trigger('change', true);
                        }
                    }
                });

                this.updateAddressFields(address);

                this.closeModal();
            },

            updateAddressFields: function(address){
                var formId = this.options.formId,
                    regionSelector = this.options.regionSelector;
                if(this.options.telephone != 'null'){
                    $(formId + ' input[name="telephone"]').val(this.options.telephone);
                    $(formId + ' input[name="telephone"]').trigger('keyup');
                }
                $(regionSelector + ' div.selectize-dropdown-content').attr('hidden',true);
                $(regionSelector + ' div.selectize-input').trigger('click',true);

                setTimeout(function () {
                    $(formId + ' div.selectize-dropdown-content div.option[data-value="'+address.region_id+'"]').trigger('click',true);
                    $(regionSelector + ' div.selectize-dropdown-content').attr('hidden',false);
                },1000);
            },

            useSuggestedAddress: function (addressIndex) {
                var address = this.response.suggested_addresses[addressIndex];
                this.updateFormAddress(address);
                window.iwdAddressValidationIsValid = true;
                return true;
            },

            onChangeAddressList: function () {
                var self = this;
                $(document).on('change', '#shipping_address_id', function (e, isAddressValidationEvent) {
                    if (!isAddressValidationEvent) {
                        window.iwdAddressValidationIsValid = false;
                        clearTimeout(self.validateAddressTimeout);
                        self.validateAddressTimeout = setTimeout(function () {
                            self.readAddress();
                            self.validateAddress();
                        }, 1000);
                    }
                });
            },

            onCheckCandidateAddress: function () {
                $(document).on('change', '#iwd_opc_address_validation_select', function () {
                    if ($(this).val()) {
                        $('#iwd_address_validation_continue_button').closest('.field').show();
                    } else {
                        $('#iwd_address_validation_continue_button').closest('.field').hide();
                    }
                });
            },

            onFillAddressForm: function () {
                var self = this;
                var shippingFields = this.options.formId + ' input, ' + this.options.formId + ' select';
                var map = self.toArray(self.options.addressMap);

                $(document).on('input change', shippingFields, function (e, isAddressValidationEvent) {
                    if (!isAddressValidationEvent) {
                        if (map.indexOf($(this).attr('name')) !== -1) {
                            window.iwdAddressValidationIsValid = false;
                            clearTimeout(self.validateAddressTimeout);
                            self.validateAddressTimeout = setTimeout(function () {
                                self.readAddress();
                                self.validateAddress();
                            }, 1000);
                        }
                    }
                });
            },

            readAddress: function () {
                var address = {};
                var formId = this.options.formId;

                address['street'] = '';
                $.each(this.options.addressMap, function (i, e) {
                    var elem = $(formId + ' [name="' + e + '"]');
                    if (elem && elem.length > 0) {
                        if (i.indexOf('street') !== -1) {
                            address['street'] += ' ' + elem.val();
                        } else {
                            address[i] = elem.val();
                        }
                    }
                });

                if ($('#shipping_address_id').length && $('#shipping_address_id').val()) {
                    var addressQuote = quote.shippingAddress();

                    address['postcode'] = addressQuote.postcode;
                    address['city'] = addressQuote.city;
                    address['country_id'] = addressQuote.countryId;
                    address['region'] = addressQuote.region;
                    address['region_id'] = addressQuote.regionId;

                    var street = '';
                    $.each(addressQuote.street, function (i, e) {
                        street += ' ' + e;
                    });
                    address['street'] = street.trim();
                }

                var realAddress = quote.shippingAddress();
                this.options.telephone = realAddress.telephone;
                $(formId + ' input[name="telephone"]').val(this.options.telephone);
                $(formId + ' input[name="telephone"]').trigger('keyup');

                return this.options.address = address;
            },

            beforeValidAddress: function () {
                fullScreenLoader.startLoader();
            },

            afterValidAddress: function () {
                fullScreenLoader.stopLoader();
            },

            whenAddressInvalid: function () {
                window.iwdAddressValidationIsValid = this.allowInvalidAddress;
                this.showModal();
            },

            checkIsAddressFilled: function () {
                var empty = [];

                $.each(this.options.address, function (i, e) {
                    if (!e || e.length === 0 || e === 0) {
                        empty.push(i);
                    }
                });

                if (empty.length === 0) {
                    return true;
                }

                if (empty.length > 2) {
                    return false;
                }

                if (empty.length > 1) {
                    return (empty.indexOf('region_id') !== -1 && empty.indexOf('region') !== -1);
                }

                return !(empty.indexOf('region_id') === -1) || !(empty.indexOf('region') === -1);
            },

            checkIsAddressValid: function () {
                var self = this,
                    countryValue = $(this.options.formId + ' select[name="country_id"]').val();

                if (this.request && this.request.readystate !== 4) {
                    this.request.abort();
                }

                if ((this.options.addressValidationProcessor === 'usps'
                        || this.options.addressValidationProcessor === 'ups')
                    && (!countryValue || countryValue.toLowerCase() !== 'us')) {
                    return this;
                }

                this.request = $.ajax({
                    url: this.options.urlValidation,
                    data: this.options.address,
                    type: 'post',
                    dataType: 'json',
                    context: this,
                    beforeSend: function () {
                        self.beforeValidAddress();
                    },
                    complete: function () {
                        self.afterValidAddress();
                    }
                }).done(function (response) {
                    self.response = response;
                    if (self.response.error) {
                        self._showError();
                    }

                    if (self.response.is_valid) {
                        this.whenAddressValid();
                    } else {
                        this.whenAddressInvalid();
                    }
                }).fail(function (error) {
                    self._showError(error);
                });
            },


            toArray: function (obj) {
                var dataArray = [];
                for (var o in obj) {
                    if (obj.hasOwnProperty(o)) {
                        dataArray.push(obj[o]);
                    }
                }
                return dataArray;
            },

            showModal: function () {
                var self = this;
                $('.iwd_opc_popup_wrapper').addClass('active').find('.iwd_opc_popup_content').focus().html(self.response.modal_content)
                    .promise().done(function () {
                    $('#iwd_opc_address_validation_select').selectize({
                        onInitialize: function () {
                            this.$control_input.addClass('input-text');
                        },
                        onFocus: function() {
                            if(this.$control_input.length) {
                                this.$control_input.attr('readonly', true).hide();
                            }
                        }
                    });
                });
            },

            closeModal: function () {
                $('.iwd_opc_popup_wrapper').removeClass('active').find('.iwd_opc_popup_content').html('');
            },

            whenAddressValid: function () {
                window.iwdAddressValidationIsValid = true;
            },

            _showError: function () {

            }
        });

        return $.mage.iwdAddressValidationCheckout;
    });