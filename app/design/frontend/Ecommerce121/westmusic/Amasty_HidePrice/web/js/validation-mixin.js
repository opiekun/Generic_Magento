define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.validation', widget, {
            listenFormValidateHandler: function (event, validation) {
                var firstActive = $(validation.errorList[0].element || []),
                    lastActive = $(validation.findLastActive() || validation.errorList.length && validation.errorList[0].element || []),
                    parent, windowHeight, successList,
                    noFocus = validation.currentForm.dataset.nofocus ? validation.currentForm.dataset.nofocus : false;

                if (lastActive.is(':hidden')) {
                    parent = lastActive.parent();
                    windowHeight = $(window).height();
                    $('html, body').animate({
                        scrollTop: parent.offset().top - windowHeight / 2
                    });
                }

                // ARIA (removing aria attributes if success)
                successList = validation.successList;

                if (successList.length) {
                    $.each(successList, function () {
                        $(this)
                            .removeAttr('aria-describedby')
                            .removeAttr('aria-invalid');
                    });
                }

                // Override -- Added focus status for form to include disabling focus functionality //
                if (firstActive.length && !noFocus) {
                    if (!firstActive.parents('.amcform-popup').length) {
                        $('html, body').stop().animate({
                            scrollTop: firstActive.offset().top
                        });
                    }
                    firstActive.focus();
                } else {
                    $(window).unbind('scroll');
                }
                // Override //
            }
        });

        return $.mage.validation;
    }
});
