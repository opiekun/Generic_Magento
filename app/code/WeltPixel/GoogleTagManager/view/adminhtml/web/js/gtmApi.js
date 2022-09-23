define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'jquery/ui',
    'mage/translate'
], function ($, alert) {
    "use strict";

    var GTMAPI = GTMAPI || {};

    var optionsForm = $('#config-edit-form');

    var triggerButton = $('#save_gtm_api'),
        triggerJsonGenerateButton = $('#generate_gtm_api_json'),
        accountID = $('#weltpixel_googletagmanager_api_account_id'),
        containerID = $('#weltpixel_googletagmanager_api_container_id'),
        uaTrackingID = $('#weltpixel_googletagmanager_api_ua_tracking_id'),
        ipAnonymization = $('#weltpixel_googletagmanager_api_ip_anonymization'),
        displayAdvertising = $('#weltpixel_googletagmanager_api_display_advertising'),
        enableConversionTracking = $('#weltpixel_googletagmanager_adwords_conversion_tracking_enable'),
        enableAdwordsRemarketing = $('#weltpixel_googletagmanager_adwords_remarketing_enable'),
        jsonExportPublicId = $("#weltpixel_googletagmanager_json_export_public_id"),
        formKey = $('#api_form_key');

    var conversionTrackingButton = $('#save_gtm_api_conversion_tracking'),
        conversionId = $('#weltpixel_googletagmanager_adwords_conversion_tracking_google_conversion_id'),
        conversionLabel = $('#weltpixel_googletagmanager_adwords_conversion_tracking_google_conversion_label'),
        conversionCurrencyCode = $('#weltpixel_googletagmanager_adwords_conversion_tracking_google_conversion_currency_code');

    var remarketingButton = $('#save_gtm_api_remarketing'),
        remarketingConversionCode = $('#weltpixel_googletagmanager_adwords_remarketing_conversion_code'),
        remarketingConversionLabel = $('#weltpixel_googletagmanager_adwords_remarketing_conversion_label');


    GTMAPI.initializeJsonGeneration = function(itemJsonGenerationUrl) {
        var that = this;
        $(triggerJsonGenerateButton).click(function() {
            $('.use-default .checkbox').each(function() {
                if ($(this).is(':checked')) {
                    $(this).trigger('click').addClass('forced-click');
                }
            });
            var validation = that._validateInputs();
            if (!validation.length) {
                validation = that._validateJsonExportInputs();
            }

            if (!validation.length && (parseInt(enableAdwordsRemarketing.val()) == 1)) {
                validation = that._validateRemarketingInputs();
            }
            if (!validation.length && (parseInt(enableConversionTracking.val()) ==  1)) {
                validation = that._validateConversionTrackingInputs();
            }

            if (validation.length) {
                alert({content: validation.join('')});
            } else {
                $.ajax({
                    showLoader: true,
                    url: itemJsonGenerationUrl,
                    data: {
                        'form_key' : formKey.val(),
                        'account_id' : accountID.val().trim(),
                        'container_id' : containerID.val().trim(),
                        'ua_tracking_id' : uaTrackingID.val().trim(),
                        'ip_anonymization' : ipAnonymization.val(),
                        'display_advertising' : displayAdvertising.val(),
                        'conversion_enabled' : enableConversionTracking.val(),
                        'conversion_id' : conversionId.val().trim(),
                        'conversion_label' : conversionLabel.val().trim(),
                        'conversion_currency_code' : conversionCurrencyCode.val().trim(),
                        'remarketing_enabled' : enableAdwordsRemarketing.val(),
                        'remarketing_conversion_code' : remarketingConversionCode.val().trim(),
                        'remarketing_conversion_label' : remarketingConversionLabel.val().trim(),
                        'public_id' : jsonExportPublicId.val().trim(),
                        'form_data' : optionsForm.serialize()
                    },
                    type: "POST",
                    dataType: 'json'
                }).done(function (data) {
                    alert({content: data.msg.join('<br/>')});
                    if (data.jsonUrl) {
                        $('#download_gtm_json').show();
                    } else {
                        $('#download_gtm_json').hide();
                    }
                    $('.use-default .checkbox.forced-click').each(function() {
                        $(this).trigger('click').removeClass('forced-click');
                    });
                    $('.use-default .checkbox.forced-click').trigger('click').removeClass('forced-click');
                });
            }
        });
    };

    GTMAPI.initialize = function (itemPostUrl) {
        var that = this;
        $(triggerButton).click(function() {
            $('.use-default .checkbox').each(function() {
                if ($(this).is(':checked')) {
                    $(this).trigger('click').addClass('forced-click');
                }
            });
            var validation = that._validateInputs();
            if (validation.length) {
                alert({content: validation.join('')});
            } else {
                $.ajax({
                    showLoader: true,
                    url: itemPostUrl,
                    data: {
                        'form_key' : formKey.val(),
                        'account_id' : accountID.val().trim(),
                        'container_id' : containerID.val().trim(),
                        'ua_tracking_id' : uaTrackingID.val().trim(),
                        'ip_anonymization' : ipAnonymization.val(),
                        'display_advertising' : displayAdvertising.val(),
                        'form_data' : optionsForm.serialize()
                    },
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        setTimeout(function(){
                            var progressHtml = '<div id="gtm-progressbar"><div class="gtm-progress-label">Loading...</div></div>';
                            $('.loading-mask').append(progressHtml);
                            $('.loading-mask .popup-inner').append('<span class="long-operation-label">this operation may take up to 5 minutes</span>');
                            var progressbar = $( "#gtm-progressbar" ),
                                progressLabel = $( ".gtm-progress-label" );
                            progressbar.progressbar({
                                value: false,
                                change: function() {
                                    progressLabel.text( progressbar.progressbar( "value" ) + "%" );
                                }
                            });
                            function progress() {
                                var val = progressbar.progressbar( "value" ) || 0;
                                progressbar.progressbar( "value", val + 1 );
                                if ( val < 99 ) {
                                    setTimeout( progress, 3800 );
                                }
                            }
                            progress();
                        }, 1500);
                    }
                }).done(function (data) {
                    $('#gtm-progressbar').remove();
                    $('.long-operation-label').remove();
                    alert({content: data.join('<br/>')});
                    $('.use-default .checkbox.forced-click').each(function() {
                        $(this).trigger('click').removeClass('forced-click');
                    });
                    $('.use-default .checkbox.forced-click').trigger('click').removeClass('forced-click');
                });
            }
        });

        var url = window.location.href;
        var newUrl = url.split('?')[0];
        if (url != newUrl) {
            window.history.pushState({}, window.document.title, newUrl);
        }
    };

    GTMAPI.initializeConversionTracking = function (itemPostUrl) {
        var that = this;
        $(conversionTrackingButton).click(function() {
            var validation = that._validateConversionTrackingInputs();
            if (validation.length) {
                alert({content: validation.join('')});
            } else {
                $.ajax({
                    showLoader: true,
                    url: itemPostUrl,
                    data: {
                        'form_key' : formKey.val(),
                        'account_id' : accountID.val().trim(),
                        'container_id' : containerID.val().trim(),
                        'conversion_id' : conversionId.val().trim(),
                        'conversion_label' : conversionLabel.val().trim(),
                        'conversion_currency_code' : conversionCurrencyCode.val().trim()
                    },
                    type: "POST",
                    dataType: 'json'
                }).done(function (data) {
                    alert({content: data.join('<br/>')});
                });
            }
        });
    };

    GTMAPI.initializeRemarketing = function (itemPostUrl) {
        var that = this;
        $(remarketingButton).click(function() {
            var validation = that._validateRemarketingInputs();
            if (validation.length) {
                alert({content: validation.join('')});
            } else {
                $.ajax({
                    showLoader: true,
                    url: itemPostUrl,
                    data: {
                        'form_key' : formKey.val(),
                        'account_id' : accountID.val().trim(),
                        'container_id' : containerID.val().trim(),
                        'conversion_code' : remarketingConversionCode.val().trim(),
                        'conversion_label' : remarketingConversionLabel.val().trim()
                    },
                    type: "POST",
                    dataType: 'json'
                }).done(function (data) {
                    alert({content: data.join('<br/>')});
                });
            }
        });
    };

    GTMAPI._validateInputs = function () {
        var errors = [];
        if (accountID.val().trim() == '') {
            errors.push($.mage.__('Please specify the Account ID') + '<br/>');
        }
        if (containerID.val().trim() == '') {
            errors.push($.mage.__('Please specify the Container ID') + '<br/>');
        }
        if (uaTrackingID.val().trim() == '') {
            errors.push($.mage.__('Please specify the Universal Tracking ID') + '<br/>');
        }

        var dimensionOptions = [
            [
                '#weltpixel_googletagmanager_general_custom_dimension_customerid',
                '#weltpixel_googletagmanager_general_custom_dimension_customerid_indexnumber',
                $.mage.__('Customer ID Index Number')
            ],
            [
                '#weltpixel_googletagmanager_general_custom_dimension_customergroup',
                '#weltpixel_googletagmanager_general_custom_dimension_customergroup_indexnumber',
                $.mage.__('Customer Group Index Number')
            ],
            [
                '#weltpixel_googletagmanager_general_custom_dimension_pagetype',
                '#weltpixel_googletagmanager_general_custom_dimension_pagetype_indexnumber',
                $.mage.__('Page Type Index Number')
            ],
            [
                '#weltpixel_googletagmanager_general_track_stockstatus',
                '#weltpixel_googletagmanager_general_track_stockstatus_indexnumber',
                $.mage.__('Track Stock Status Index Number')
            ],
            [
                '#weltpixel_googletagmanager_general_track_reviewscount',
                '#weltpixel_googletagmanager_general_track_reviewscount_indexnumber',
                $.mage.__('Track Reviews Count Index Number')
            ],
            [
                '#weltpixel_googletagmanager_general_track_reviewsscore',
                '#weltpixel_googletagmanager_general_track_reviewsscore_indexnumber',
                $.mage.__('Track Reviews Score Index Number')
            ],
            [
                '#weltpixel_googletagmanager_general_track_saleproduct',
                '#weltpixel_googletagmanager_general_track_saleproduct_indexnumber',
                $.mage.__('Track Sale Product Number')
            ]
        ];

        var mixedOptions = [
            [
                '#weltpixel_googletagmanager_general_track_custom_attribute_1',
                '#weltpixel_googletagmanager_general_track_custom_attribute_1_indexnumber',
                '#weltpixel_googletagmanager_general_track_custom_attribute_1_type',
                $.mage.__('Track Custom Attribute 1 Index Number')
            ],
            [
                '#weltpixel_googletagmanager_general_track_custom_attribute_2',
                '#weltpixel_googletagmanager_general_track_custom_attribute_2_indexnumber',
                '#weltpixel_googletagmanager_general_track_custom_attribute_2_type',
                $.mage.__('Track Custom Attribute 2 Index Number')
            ],
            [
                '#weltpixel_googletagmanager_general_track_custom_attribute_3',
                '#weltpixel_googletagmanager_general_track_custom_attribute_3_indexnumber',
                '#weltpixel_googletagmanager_general_track_custom_attribute_3_type',
                $.mage.__('Track Custom Attribute 3 Index Number')
            ],
            [
                '#weltpixel_googletagmanager_general_track_custom_attribute_4',
                '#weltpixel_googletagmanager_general_track_custom_attribute_4_indexnumber',
                '#weltpixel_googletagmanager_general_track_custom_attribute_4_type',
                $.mage.__('Track Custom Attribute 4 Index Number')
            ],
            [
                '#weltpixel_googletagmanager_general_track_custom_attribute_5',
                '#weltpixel_googletagmanager_general_track_custom_attribute_5_indexnumber',
                '#weltpixel_googletagmanager_general_track_custom_attribute_5_type',
                $.mage.__('Track Custom Attribute 5 Index Number')
            ]
        ];

        var dimensionValues = [];
        var metricsValues = [];

        for (var i=0; i<dimensionOptions.length; i++) {
            if ($(dimensionOptions[i][0]).val() == '1') {
                var dimensionVal = $(dimensionOptions[i][1]).val();
                if (dimensionValues[dimensionVal]) {
                    dimensionValues[dimensionVal] = [dimensionValues[dimensionVal], dimensionOptions[i][2]].join(', ');
                } else {
                    dimensionValues[dimensionVal] = dimensionOptions[i][2];
                }
            }
        }

        for (var i=0; i<mixedOptions.length; i++) {
            if ($(mixedOptions[i][0]).val() == '1') {
                var mixedOptionVal = $(mixedOptions[i][1]).val();
                var mixedOptionType = $(mixedOptions[i][2]).val();
                var optionTypeStorage = dimensionValues;

                if (mixedOptionType == 'metric') {
                    optionTypeStorage = metricsValues;
                }

                if (optionTypeStorage[mixedOptionVal]) {
                    optionTypeStorage[mixedOptionVal] = [optionTypeStorage[mixedOptionVal], mixedOptions[i][3]].join(', ');
                } else {
                    optionTypeStorage[mixedOptionVal] = mixedOptions[i][3];
                }
            }
        }

        for (var i=0; i<100; i++) {
            if (dimensionValues[i] && dimensionValues[i].length) {
                var dimensions = dimensionValues[i].split(',');
                if (dimensions.length > 1) {
                    errors.push($.mage.__('Dimension Value') + ' ' + i + ' ' + $.mage.__("is the same for:") + ' ' + dimensionValues[i] + '<br/>');
                }
            }
            if (metricsValues[i] && metricsValues[i].length) {
                var metrics = metricsValues[i].split(',');
                if (metrics.length > 1) {
                    errors.push($.mage.__('Metric Value') + ' ' + i + ' ' + $.mage.__("is the same for:") + ' ' + metricsValues[i] + '<br/>');
                }
            }
        }

        return errors;
    };

    GTMAPI._validateConversionTrackingInputs = function () {
        var errors = [];
        if (accountID.val().trim() == '') {
            errors.push($.mage.__('Please specify the Account ID in GTM API Configuration section') + '<br/>');
        }
        if (containerID.val().trim() == '') {
            errors.push($.mage.__('Please specify the Container ID in GTM API Configuration section') + '<br/>');
        }
        if (conversionId.val().trim() == '') {
            errors.push($.mage.__('Please specify the Google Conversion Id') + '<br/>');
        }
        if (conversionLabel.val().trim() == '') {
            errors.push($.mage.__('Please specify the Google Conversion Label') + '<br/>');
        }
        if (conversionCurrencyCode.val().trim() == '') {
            errors.push($.mage.__('Please specify the Google Convesion Currency Code') + '<br/>');
        }

        return errors;
    };

    GTMAPI._validateJsonExportInputs = function() {
        var errors = [];
        if (jsonExportPublicId.val().trim() == '') {
            errors.push($.mage.__('Please specify the Public Id') + '<br/>');
        }
        return errors;
    };

    GTMAPI._validateRemarketingInputs = function () {
        var errors = [];
        if (accountID.val().trim() == '') {
            errors.push($.mage.__('Please specify the Account ID in GTM API Configuration section') + '<br/>');
        }
        if (containerID.val().trim() == '') {
            errors.push($.mage.__('Please specify the Container ID in GTM API Configuration section') + '<br/>');
        }
        if (remarketingConversionCode.val().trim() == '') {
            errors.push($.mage.__('Please specify the Conversion Code') + '<br/>');
        }
        return errors;
    };

    return GTMAPI;
});