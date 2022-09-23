define([
    'jquery',
    './ninjamenus',
], function ($) {
    'use strict';

    $.widget('mgz.ninjamenustop', $.mgz.ninjamenus, {
        

        _init: function () {
            var self = this;

            this._super();
            this._assignControls()._listen();

            // $(window).resize(function () {
            //     if ($(this).width() <= self.options.mobileBreakpoint) {
            //         self.element.find('.mgz-tabs').trigger('deactiveTab');
            //     } else {
            //         self.element.find('.mgz-tabs').trigger('activeTab', 0);
            //     }
            // }).resize();
            // self.element.find('.mgz-tabs').each(function(index1, el) {
            //     var tabs = $(this);
            //     $(this).find('.mgz-tabs-content > .mgz-tabs-tab-title').each(function(index2, el) {
            //         $(this).off('click');
            //         $(this).click(function(event) {
            //             if ($(this).hasClass('mgz-active')) {
            //                 tabs.trigger('deactiveTab', index2);
            //             } else {
            //                 tabs.trigger('activeTab', index2);
            //             }
            //             return false;
            //         });
            //     });
            // });
        },

        /**
         * @return {Object}
         * @private
         */
        _assignControls: function () {
            this.controls = {
                toggleBtn: $('[data-action="toggle-nav"]'),
                swipeArea: $('.nav-sections')
            };
            return this;
        },

        /**
         * @private
         */
        _listen: function () {
            var controls = this.controls,
                toggle = this.toggle;
            if (!controls.toggleBtn.hasClass('ninjamenus-top-triggered')) {
                this._on(controls.toggleBtn, {
                    'click': toggle
                });
                this._on(controls.swipeArea, {
                    'swipeleft': toggle
                });
                controls.toggleBtn.addClass('ninjamenus-top-triggered');
            }
        },

        /**
         * Toggle.
         */
        toggle: function () {
            var html = $('html');
            if (html.hasClass('nav-open')) {
                html.removeClass('nav-open');
                setTimeout(function () {
                    html.removeClass('nav-before-open');
                }, this.options.hideDelay);
            } else {
                html.addClass('nav-before-open');
                setTimeout(function () {
                    html.addClass('nav-open');
                }, this.options.showDelay);
            }
        },

        initStickMenu: function () {
            if (this.element.parents('.nav-sections')) {
                this._initScrollToFixed(this.element.parents('.nav-sections'));
            }
        },

        onMouseHover: function(item) {
            this._super(item);
            if (this.isDesktop && item.hasClass('nav-item-static')) {
                item.closest('.magezon-builder').addClass('nav-item-static');
                item.closest('.ninjamenus').addClass('nav-item-static');
                item.closest('.navigation').addClass('nav-item-static');
            }
        },

        onMouseLeave: function(item) {
            this._super(item);
            if (!this.isMobile && item.hasClass('nav-item-static')) {
                var animationDuration = item.data('animation-duration') ? item.data('animation-duration') : 0;
                setTimeout(function() {
                    item.closest('.magezon-builder').removeClass('nav-item-static');
                    item.closest('.ninjamenus').removeClass('nav-item-static');
                    item.closest('.navigation').removeClass('nav-item-static');
                }, animationDuration);
            }
        }
    });

    return $.mgz.ninjamenustop;
});
