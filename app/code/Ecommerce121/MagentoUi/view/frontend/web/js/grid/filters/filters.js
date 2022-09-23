define([
    'jquery'
], function ($) {
    'use strict';

    var mixin = {
        apply: function () {
            const body = $('body');

            if ($.isFunction(body.notification)) {
                body.notification('clear');
                this.set('applied', removeEmpty(this.filters));
            }

            return this;
        },

        /**
         * Provider ajax error listener.
         *
         * @param {bool} isError - Selected index of the filter.
         */
        onBackendError: function (isError) {
            const defaultMessage = 'Something went wrong with processing the default view and we have restored the ' +
                    'filter to its original state.',
                customMessage  = 'Something went wrong with processing current custom view and filters have been ' +
                    'reset to its original state. Please edit filters then click apply.';

            if (isError) {
                this.clear();
                const body = $('body');
                if ($.isFunction(body.notification)) {
                    body.notification('clear')
                        .notification('add', {
                            error: true,
                            message: $.mage.__(this.bookmarksActiveIndex !== 'default' ? customMessage : defaultMessage),

                            /**
                             * @param {String} message
                             */
                            insertMethod: function (message) {
                                var $wrapper = $('<div/>').html(message);

                                $('.page-main-actions').after($wrapper);
                            }
                        });
                }
            }
        }
    }

    return function (target) {
        return target.extend(mixin);
    };
});
