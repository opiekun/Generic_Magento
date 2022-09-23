define([
    'jquery',
    './jquery.drilldown.min',
    './jquery.hoverIntent.min',
    ], function ($) {
        'use strict';

        $.widget('mgz.ninjamenus', {

            options: {
                submenuSelector: ".item-submenu",
                openerSelector: ".opener",
                mobileBreakpoint: 768,
                mobileClasses: "ninjamenus-mobile",
                desktopClasses: "ninjamenus-desktop",
                ddAnimationDurationIn: 50,
                stick: false,
                caret: 'fas mgz-fa-angle-down',
                caretHover: 'fas mgz-fa-angle-up',
                openerHtml: '<span class="opener"></span>'
            },
            isMobile: false,
            isDesktop: false,

        /**
         * @private
         */
         _create: function () {
            this.menu = this.element;
            this.menu.find('.magezon-builder > .nav-item').addClass('level0');
            this.initListeners();
            this.initAccordion();
            this.loadLazyImages(this.menu, this.options.ddAnimationDurationIn * 2);
            this.initStickMenu();
        },

        _init: function () {
            this._super();
            this._setActiveMenu();
        },

        getType: function() {
            return this.menu.data('type');
        },

        getMobileType: function() {
            return this.menu.data('mobile-type');
        },

        enableDrillDown: function() {
            this.menu.addClass('ninjamenus-drilldown');
            this.menu.find(this.options.openerSelector).addClass('drilldown-opener');
            this.menu.find('.ninjamenus-drilldown-back').show();
            if (this.menu.parent().data('drilldown')) {
                this.menu.parent().drilldown('reset');
            }
            this.menu.find('.ninjamenus-drilldown-back,.item-submenu').css('display', '').css('width', '');
            this.menu.parent().drilldown({
                selector: '.drilldown-opener',
                cssClass: {
                    container: this.options.id + '-drilldown-container',
                    root: this.options.id + '-drilldown-root',
                    sub: 'item-submenu',
                    back: 'drilldown-back'
                },
                speed: 300
            });

            this.menu.find('.ninjamenus-lazy').each(function (index, el) {
                if (!$(this).hasClass('ninjamenus-lazy-loaded')) {
                    $(this).removeClass('ninjamenus-lazy-blur');
                    var src = $(this).data('src');
                    if (src) {
                        $(this).attr('src', src);
                        $(this).removeClass('ninjamenus-lazy-blur').addClass('ninjamenus-lazy-loaded');
                    }
                    $('.ninjamenus img[data-src="' + src + '"').attr('src', src);
                    $('.ninjamenus img[data-src="' + src + '"').removeClass('ninjamenus-lazy-blur').addClass('ninjamenus-lazy-loaded');
                }
            });
        },

        disableDrillDown: function() {
            this.menu.removeClass('ninjamenus-drilldown');
            this.menu.find(this.options.openerSelector).removeClass('drilldown-opener');
            this.menu.find('.ninjamenus-drilldown-back').hide();
            this.menu.parent().drilldown('reset');
        },

        initListeners: function () {
            var self       = this;
            var type       = this.getType();
            var mobileType = this.getMobileType();

            if (type == 'drilldown' || mobileType == 'drilldown') {
                this.enableDrillDown();
            }

            this.menu.find('.drilldown-opener').on('click', function() {
                self.loadLazyImages(self.menu);
            });

            this.menu.find('.mgz-tabs-tab-title').on('hover click', function() {
                let item = $(this).closest('.level0');
                self.loadLazyImages(item);
            });

            self.menu.find('.nav-item').each(function(index, el) {
                var caret = self.getCaretIcon($(this));
                if (caret) {
                    $(this).children('a').children('.caret').addClass(caret);
                }

                if ($(this).children(self.options.submenuSelector).length && !$(this).children('.opener').length) {
                    $(this).children('a').after(self.options.openerHtml);
                }
            });

            $(window).resize(function () {
                if ($(this).width() < self.options.mobileBreakpoint) {
                    if (mobileType == 'drilldown') {
                        self.enableDrillDown();
                    } else {
                        self.disableDrillDown();
                    }
                    self.isMobile  = true;
                    self.isDesktop = false;
                    self.menu.parents('.nav-sections').addClass(self.options.mobileClasses + '-wrapper');
                    self.menu.addClass(self.options.mobileClasses);
                    self.menu.parents('.nav-sections').removeClass(self.options.desktopClasses + '-wrapper');
                    self.menu.removeClass(self.options.desktopClasses);
                    self.menu.parent().find('.ninjamenus-hamburger-trigger').show();
                    self.menu.find(self.options.submenuSelector).each(function(index, el) {
                        if ($(this).children('.mgz-hidden-sm').length === $(this).children('.mgz-element').length) {
                            $(this).closest('.nav-item').children('.opener').hide();
                        }
                    });
                    self.menu.find('.level0').removeClass('nav-item-static');
                } else {
                    if (type == 'drilldown') {
                        self.enableDrillDown();
                    } else {
                        self.disableDrillDown();
                    }
                    self.isMobile  = false;
                    self.isDesktop = true;
                    self.menu.parents('.nav-sections').removeClass(self.options.mobileClasses + '-wrapper');
                    self.menu.removeClass(self.options.mobileClasses);
                    self.menu.parents('.nav-sections').addClass(self.options.desktopClasses + '-wrapper');
                    self.menu.addClass(self.options.desktopClasses);
                    self.menu.parent().find('.ninjamenus-hamburger-trigger').hide();
                    $('.ninjamenus .item-submenu').css('display', '');
                    self.menu.find('.nav-item').removeClass('ninjamenus-toggle-active');
                }

                if (!self.isIpad()) {
                    self.menu.find('.ninjamenus-tablet').removeClass('ninjamenus-tablet');
                }
            }).resize();

            if (type == 'accordion' || type == 'drilldown') {

            } else {
                if (self.options.hasOwnProperty('hoverDelayTimeout')) {
                    $('.nav-item', this.menu).hoverIntent({
                        sensitivity: 2, 
                        interval: 100, 
                        over: self.onMouseHoverIntent.bind(this), 
                        timeout: self.options.hoverDelayTimeout, 
                        out: self.onMouseLeaveIntent.bind(this)
                    });
                } else {
                    this.menu.on('mouseenter', '.nav-item', function (e) {
                        self.onMouseHover($(this));
                    });
                    this.menu.on('mouseleave', '.nav-item', function (e) {
                        self.onMouseLeave($(this));
                    });
                }
            }
            this.menu.on('click', '.nav-item > a', function (e) {
                if ($(this).data('scrollto') && $($(this).data('scrollto')).length) {
                    $('html, body').animate({
                        scrollTop: $($(this).data('scrollto')).offset().top
                    }, 1000);
                    return false;
                }
                var parent = $(this).closest('.nav-item');
                var subMenu = parent.children(self.options.submenuSelector);
                if (self.isDesktop && self.isIpad()) {
                    self.loadLazyImages(parent);
                    self._showDropdown(parent);
                    self._caretHover(parent);
                    self._iconHover(parent);
                    if (subMenu.length) {
                        if (parent.hasClass('center')) {
                            var width = '-' + ((subMenu.outerWidth() - parent.outerWidth()) / 2) + 'px';
                            subMenu.css('margin-left', width);
                        }
                        if ((subMenu.offset().left < 0) || ((subMenu.offset().left + subMenu.width()) > self.menu.width())) {
                            parent.addClass('ninjamenus-tablet');
                        } else {
                            parent.removeClass('ninjamenus-tablet');
                        }
                    }
                }

                if (self.isIpad()) {
                    if (parent.hasClass('level0') && parent.children('.item-submenu').length) {
                        return false;
                    }
                }
            });

            this.menu.parent().on("click", ".menu-trigger-inner", function (e) {
                $(this).parent().parent().toggleClass('ninjamenus-hamburger-active');
            });
        },

        onMouseHoverIntent: function(event) {
            var item = $(event.currentTarget);
            this.onMouseHover(item);
        },

        onMouseHover: function(item) {
            item.addClass('item-hovered');
            if (this.isDesktop) {
                this.loadLazyImages(item);
                this._showDropdown(item);
                this._caretHover(item);
                this._iconHover(item);
                if (item.hasClass('center')) {
                    var width = '-' + ((item.children(this.options.submenuSelector).outerWidth() - item.outerWidth()) / 2) + 'px';
                    item.children(this.options.submenuSelector).css('margin-left', width);
                }
            } else {
                item.children(this.options.submenuSelector).css('margin-left', '');
            }
        },

        onMouseLeaveIntent: function(event) {
            var item = $(event.currentTarget);
            this.onMouseLeave(item);
        },

        onMouseLeave: function(item) {
            item.removeClass('item-hovered');
            if (!this.isMobile) {
                this._hideDropdown(item);
                this._caret(item);
                this._icon(item);
            }
        },

        isIpad: function() {
            //lib/web/fotorama/fotorama.js - line 498
            return window.navigator.userAgent.match(/iPad/i);
            //return window.navigator.userAgent.match(/Android|webOS|iPhone|iPad|iPod|BlackBerry|Windows Phone/i);
        },

        getCaretIcon: function(item) {
            var icon;
            if (item.attr('data-caret')) {
                icon = item.data('caret');
            } else {
                icon = this.options.caret;
            }
            return icon;
        },

        getCaretHoverIcon: function(item) {
            var icon;
            if (item.attr('data-caret-hover')) {
                icon = item.data('caret-hover');
            } else {
                icon = this.options.caretHover;
            }
            return icon;
        },

        _caret: function (item) {
            var caretSelector = item.children('a').find('.caret');
            if (caretSelector.length) {
                var caret      = this.getCaretIcon(item);
                var caretHover = this.getCaretHoverIcon(item);
                if (caret && caretHover) {
                    var classes = caretSelector.attr('class').replace(caretHover, caret);
                    caretSelector.attr('class', classes);
                }
            }
        },

        _caretHover: function (item) {
            var caretSelector = item.children('a').find('.caret');
            if (caretSelector.length) {
                var caret      = this.getCaretIcon(item);
                var caretHover = this.getCaretHoverIcon(item);
                if (caret && caretHover) {
                    var classes = caretSelector.attr('class').replace(caret, caretHover);
                    caretSelector.attr('class', classes);
                }
            }
        },

        _icon: function (item) {
            var iconSelector = item.children('a').find('.item-icon');
            if (iconSelector.length) {
                var icon      = item.data('icon');
                var iconHover = item.data('icon-hover');
                if (icon && iconHover) {
                    var classes = iconSelector.attr('class').replace(iconHover, icon);
                    iconSelector.attr('class', classes);
                }
            }
        },

        _iconHover: function (item) {
            var iconSelector = item.children('a').find('.item-icon');
            if (iconSelector.length) {
                var icon      = item.data('icon');
                var iconHover = item.data('icon-hover');
                if (icon && iconHover) {
                    var classes = iconSelector.attr('class').replace(icon, iconHover);
                    iconSelector.attr('class', classes);
                }
            }
        },

        _showDropdown: function (item) {
            var submenuSelector = item.children(this.options.submenuSelector);
            if (!submenuSelector.is(':visible') && submenuSelector.length) {
                var animateOut = item.data('animate-out');
                if (animateOut) submenuSelector.removeClass(animateOut);
                var animateIn = item.data('animate-in');
                if (animateIn) submenuSelector.addClass('animated ' + animateIn);
                item.addClass('ninjamenus-hover');
                var submenuInnerSelector = item.closest(this.options.submenuSelector);
                var spacing = submenuInnerSelector.outerWidth(true) - ((submenuInnerSelector.outerWidth(true) - submenuInnerSelector.width()) / 2);
                if (!spacing) spacing = submenuInnerSelector.outerWidth(true);
                if (item.hasClass('left_vertical_full_height') || item.hasClass('right_vertical_full_height')) {
                    var minHeight = item.parents('.magezon-builder').height();
                    submenuSelector.css('min-height', minHeight);
                    if (item.hasClass('left_vertical_full_height')) {
                        submenuSelector.css('right', spacing);
                    }
                    if (item.hasClass('right_vertical_full_height')) {
                        submenuSelector.css('left', spacing);
                    }
                }
            }
        },

        _hideDropdown: function (item) {
            var self = this;
            var submenuSelector = item.children(this.options.submenuSelector);
            if (submenuSelector.is(':visible') && submenuSelector.length) {
                var animateIn         = item.data('animate-in');
                var animateOut        = item.data('animate-out');
                var animationDuration = item.data('animation-duration') ? item.data('animation-duration') : 0;
                if (self.isMobile) {
                    if (animateIn) submenuSelector.removeClass(animateIn);
                    if (animateOut) submenuSelector.removeClass(animateOut);
                } else {
                    if (animateIn) submenuSelector.removeClass(animateIn);
                    if (animateOut) {
                        submenuSelector.addClass('animated ' + animateOut);
                        setTimeout(function() {
                            item.removeClass('ninjamenus-hover');
                        }, animationDuration);
                    } else {
                        item.removeClass('ninjamenus-hover');
                    }
                }
            }
        },

        _setActiveMenu: function () {
            var self       = this;
            var currentUrl = window.location.href.split('?')[0];
            var link       = this.menu.find('.nav-item > a[href="' + currentUrl + '"]');
            var type       = this.getType();
            link.parent().addClass('active');
            link.parents().addClass('active');
            if (type == 'accordion') {
                setTimeout(function() {
                    link.parents().children(self.options.openerSelector).trigger('click');
                }, 200);
            }
        },

        initAccordion: function () {
            var self       = this;
            var type       = this.getType();
            var mobileType = this.getMobileType();
            this.menu.find(this.options.openerSelector).on('click', function (e) {
                var parent = $(this).closest('.nav-item');
                if ((type == 'accordion' && self.isDesktop) || (mobileType == 'accordion' && self.isMobile)) {
                    var current  = this;
                    parent.toggleClass('ninjamenus-toggle-active');
                    parent.children(self.options.submenuSelector).stop(true, true).slideToggle(400, function() {
                        self.loadLazyImages(parent);
                    });
                    if (type == 'accordion') {
                        if (parent.hasClass('ninjamenus-toggle-active')) {
                            self._iconHover(parent);
                        } else {
                            self._icon(parent);
                        }
                    }
                    parent.siblings('.ninjamenus-toggle-active').children(self.options.submenuSelector).slideUp();
                    parent.siblings('.ninjamenus-toggle-active').removeClass('ninjamenus-toggle-active');
                    return false;
                } else {
                    self.loadLazyImages(parent, 1000);
                }
            });
        },

        loadLazyImages: function (elem, timeout) {
            if (!timeout) timeout = 100;
            var self = this;
            setTimeout(function () {
                elem.find('.ninjamenus-lazy').each(function (index, el) {
                    if (!$(this).hasClass('ninjamenus-lazy-loaded')) {
                        if ($(this).is(":visible")) {
                            $(this).removeClass('ninjamenus-lazy-blur');
                            var src = $(this).data('src');
                            if (src) {
                                $(this).attr('src', src);
                                $(this).removeClass('ninjamenus-lazy-blur').addClass('ninjamenus-lazy-loaded');
                            }
                            $('.ninjamenus img[data-src="' + src + '"').attr('src', src);
                            $('.ninjamenus img[data-src="' + src + '"').removeClass('ninjamenus-lazy-blur').addClass('ninjamenus-lazy-loaded');
                        }
                    }
                });
                elem.find(self.options.submenuSelector).each(function (index, el) {
                    if ($(this).data('background-image') && !$(this).hasClass('ninjamenus-lazy-loaded') && $(this).is(":visible")) {
                        var backgroundSrc = 'url(' + $(this).data('background-image') + ')';
                        $(this).css('background-image', backgroundSrc);
                        $(this).addClass('ninjamenus-lazy-loaded');
                    }
                });
            }, timeout);
        },

        initStickMenu: function () {
            if (this.menu.parent('.ninjamenus-widget')) {
                this._initScrollToFixed(this.menu.parent('.ninjamenus-widget'));
            }
        },

        _initScrollToFixed: function (element) {
            if (this.options.stick) {
                var self = this;
                require(['Magezon_Core/js/jquery-scrolltofixed-min'], function () {
                    element.scrollToFixed();
                    element.css('z-index', '');
                    $(window).resize(function () {
                        if ($(this).width() >= self.options.mobileBreakpoint) {
                            element.addClass('ninjamenus-scrolltofixed');
                            element.removeClass('ninjamenus-unscrolltofixed');
                        } else {
                            element.trigger('detach.ScrollToFixed');
                            element.css('z-index', '');
                            element.css('position', '');
                            element.css('top', '');
                            element.addClass('ninjamenus-unscrolltofixed');
                            element.removeClass('ninjamenus-scrolltofixed');
                        }
                    }).resize();
                });
            }
        }
    });

    return $.mgz.ninjamenus;
});
