define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/components/fieldset'
], function ($, _, Fieldset) {
    'use strict';

    return Fieldset.extend({
        defaults: {
            additionalClasses: '',
            firstElement: '',
            defaultElement: '',
            template: 'Magezon_UiBuilder/form/element/tab',
            active: 1,
            labelVisible: true
        },

        onElementRender: function() {
            var self       = this;
            var $hasOpened = 0;
            var i          = 0;

            var active = self.active;
            if (active > this.elems().length || active < 1) {
                active = 1;
            }

            _.each(this.elems(), function (elem) {
                if (i == (self.active-1)) {
                    self.firstElement = elem;
                }
                if (elem.opened()) {
                    self.defaultElement = elem;
                    $hasOpened++;
                }
                i++;
            });
            if ($hasOpened != 1 || !this.defaultElement) {
                this.defaultElement = this.firstElement;
            }
            this.activeDefaultTab();
        },

        activeDefaultTab() {
            if (this.defaultElement) {
                _.each(this.elems(), function (elem) {
                    elem.opened(false);
                });
                this.defaultElement.opened(true);
                this.defaultElement.visible(true);
            }
        },

        activeTab:function(tab) {
            _.each(this.elems(), function (elem) {
                elem.opened(false);
            });
            tab.visible(true);
            tab.opened(true);
        }
    });
});
