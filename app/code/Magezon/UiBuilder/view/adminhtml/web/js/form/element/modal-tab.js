define([
    'jquery',
    './tab'
], function ($, Tab) {
    'use strict';

    return Tab.extend({

        activeTab:function(tab) {
            this._super(tab);

            // Cal Height
            $(window).resize(function(event) {
                var $headerHeight = $('.uibuilder-modal .modal-component .modal-header').outerHeight();
                var $footerHeight = $('.uibuilder-modal .modal-component .uibuilder-modal-footer').outerHeight();
                var $tabTitle     = $('.uibuilder-modal .uibuilder-modal-tab .uibuilder-tab-title').outerHeight();
                var $height       = $(window).height() - $headerHeight - $footerHeight - $tabTitle - 40;
                $('.uibuilder-modal > .uibuilder-tab-content > .uibuilder-tab-content-item').height($height);
            }).resize();
        }
    });
});
