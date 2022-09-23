/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/smart-keyboard-handler',
    'mage/mage',
    'mage/ie-class-fixer',
    'domReady!'
], function ($, keyboardHandler) {
    'use strict';

    /**
     * This feature is only needed for Type B product, so I disabled it for now.
     * The hypothetical class for the new type of products will be type-b, it will
     * needs to adjust this if the class changes
    */
    $('.type-b .product.media').mage('sticky', {
         container: '#content-media-info'
    });

    $('.panel.header > .header.links').clone().appendTo('#store\\.links');

    keyboardHandler.apply();

    $('.qty-change').on('click', function () {
        var qty = $('.input-text.qty');

        if($(this).hasClass('qty-decrease')) {
            if(qty.val() > 1) {
                qty.val(parseInt(qty.val()) - 1);
            }
            else {
                qty.val(1);
            }
        }
        else if ($(this).hasClass('qty-increase')) {
            qty.val(parseInt(qty.val()) + 1);
        }
    });

    window.addEventListener("DOMContentLoaded", function() {
        setTimeout(function (){
            let acsbTriggerDesktop = $('body .acsb-trigger.acsb-trigger-size-medium');
            let acsbTriggerMobile = $('body .acsb-trigger.acsb-trigger-size-small');

            const desktopContainer = $('footer .newsletter .content ul');
            const mobileContainer = $('footer .newsletter .content ul');

            acsbTriggerDesktop.detach();
            acsbTriggerMobile.detach();

            if (document.readyState === 'complete') {
                const defStyles = {
                    'marginTop': 0,
                    'marginLeft': 0,
                    'left': 0,
                    'right': 0,
                    'bottom': 0,
                    'top': 0
                }
                acsbTriggerDesktop.css(defStyles);
                acsbTriggerMobile.css(defStyles);
                acsbTriggerDesktop.hover(function() {
                    $(this).css({
                        'transform':'none'
                    });
                });
                if ($(window).width() > 768) {
                    acsbTriggerDesktop.css({
                        'height':'45px',
                        'width':'45px'
                    });
                    desktopContainer.append('<li>');
                    desktopContainer.find('li:last-child').append(acsbTriggerDesktop);

                } else {
                    acsbTriggerMobile.css({
                        'height':'35px',
                        'width':'35px'
                    });
                    desktopContainer.append('<li>');
                    desktopContainer.find('li:last-child').append(acsbTriggerMobile);
                }
            }
        },3500)
    });

    $('.product-info-main .product.attribute.sku').click(function (event) {
        $('.page-title-wrapper.product .info-popup').css('top','110%');
        $('.page-title-wrapper.product .info-popup').show('fast');
        event.stopPropagation();
    });
    $('.product-info-main .product-info-codes').click(function (event) {
        $('.page-title-wrapper.product .info-popup').css('top','188%');
        $('.page-title-wrapper.product .info-popup').show('fast');
        event.stopPropagation();
    });
});
