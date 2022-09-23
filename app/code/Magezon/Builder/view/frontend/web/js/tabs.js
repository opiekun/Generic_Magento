define([
    'jquery',
    'jquery-ui-modules/widget'
], function ($) {
    'use strict';

    $.widget('magezon.mgzTabs', {
        _create: function () {
            var self = this;

            $('.mgz-tabs-tab-content:not(.mgz-active) .owl-carousel').addClass('mgz-carousel-hidden');

            var $tabsList    = this.element.children('.mgz-tabs-nav');
            var $tabsContent = this.element.children('.mgz-tabs-content');
            $tabsList.children('.mgz-tabs-tab-title').each(function(index, el) {
                var outerHTML = $(this)[0].outerHTML;
                var anchor    = $(this).children('a');
                var targetId  = $(this).children('a').data('id');
                if (targetId) {
                    self.element.find(targetId).before(outerHTML);
                }
            });

            var activeTab = function(tab) {
                $tabsList.children().removeClass('mgz-active');
                $tabsContent.children().removeClass('mgz-active');
                var parentId = tab.parent().attr('data-id');
                self.element.find('.' + parentId).addClass('mgz-active');
                var targetId = tab.data('id') ? tab.data('id') : tab.attr('href');
                var target = self.element.find(targetId);
                target.addClass('mgz-active');
                $(self.element).parents('.mgz-element').trigger('mgz:change');
                setTimeout(function() {
                    target.find('.owl-carousel.mgz-carousel-hidden').removeClass('mgz-carousel-hidden');
                }, 500);

                return true;
            }

            if (this.options.hover_active) {
                $tabsList.children().hover(function(e) {
                    activeTab($(this).children('a'));
                });
            }

            $tabsList.children().click(function(e) {
                if ($(this).children('a').attr('href').indexOf('#') !== -1) {
                    e.preventDefault();
                    activeTab($(this).children('a'));
                    return false;
                }
            });

            $tabsContent.children('.mgz-tabs-tab-title').click(function(e) {
                e.preventDefault();
                activeTab($(this).children('a'));
                return false;
            });
        }
    });

    return $.magezon.mgzTabs;
});