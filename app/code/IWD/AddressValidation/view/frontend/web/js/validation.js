define([
        'jquery',
        'Magento_Ui/js/modal/alert',
        'mage/translate',
        'jquery/ui'
    ],

    function ($, modal, $t){
        'use strict';

        $.widget('mage.iwdAddressValidation', {
            _initOptions:function(options){
                var self = this;

                options = options || {};
                $.each(options, function(i, e){self.options[i] = e;});
            },

            validateAddress: function(){
                if (this.checkIsAddressFilled()){
                    this.checkIsAddressValid();
                }
            },

            checkIsAddressFilled: function(){
                var empty = [];

                $.each(this.options.address, function(i, e) {
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

            checkIsAddressValid:function(){
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
                            self.beforeValidAddress();
                        },
                        complete: function() {
                            self.afterValidAddress();
                        }
                    })
                    .done(function(response) {
                        if (response.error){
                            self._showError(response);
                        }
                        if(response.is_valid){
                            this.whenAddressValid();
                        } else {
                            this.whenAddressInvalid(response);
                        }
                    })
                    .fail(function(error) {
                        self._showError(error);
                    });
            },

            showModal:function (response) {
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
                                if (self.updateAddress(response)){
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

            checkCandidateAddress:function() {
                $(document).on('change', '.iwd-address-validation-popup input[name="candidate"]', function(){
                    $('.iwd-address-validation-popup .modal-content .mage-error').remove();
                });
            },

            _showError:function(error){
                console.log(JSON.stringify(error));
            },

            toArray:function(obj){
                var dataArray = [];
                for(var o in obj) {
                    dataArray.push(obj[o]);
                }
                return dataArray;
            },

            whenAddressValid:function(){
                $('.iwd-address-validation-error-message').remove();
                $(this.options.nextStepButtonId).attr('disabled', null);
                this.enableBlocks();
            },

            whenAddressInvalid: function(response){
                this.disableNextButton(this.options.content.updateAddress);
                this.disableBlocks();
                this.showModal(response);
            },

            updateFormAddress:function(address){
                var formId = this.options.formId;
                var map = this.options.addressMap;

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
            },

            disableBlocks: function(){
                $('.iwd-loader-for-av').show();
            },
            enableBlocks: function(){
                $('.iwd-loader-for-av').hide();
            },

            disableNextButton: function(message){
                $(this.options.nextStepButtonId).attr('disabled', 'disabled');
                $('.iwd-address-validation-error-message').remove();
                $(this.options.nextStepButtonId).parent()
                    .append('<div style="clear:both"></div><div generated="true" class="iwd-address-validation-error-message mage-error">' +
                        $t(message) +
                        '</div>');
            },

            beforeValidAddress: function(){
                this.disableNextButton(this.options.content.validatingAddress);
                this.validation = 'validations';
            },

            afterValidAddress: function(){
                this.validation = true;
            },

            updateAddress:function(response){}
        });

        return $.mage.iwdAddressValidation;
    });