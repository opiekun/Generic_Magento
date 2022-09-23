define([
    'jquery',
    'uiRegistry',
    './ui-select'
], function ($, registry, Select) {
    'use strict';

    return Select.extend({
    	defaults: {
    		service: {
    			template: 'Magezon_UiBuilder/form/element/helper/animation_preview'
    		}
    	},

        initObservable: function () {
            this._super();
            this.observe('animateClass');
            this.additionalClasses += ' uibuilder-field-animation';
            return this;
        },

    	loadAnimate: function() {
    		var self = this;
    		if (this.value()) {
    			self.animateClass(this.value() + ' animated');	
    		}
    		setTimeout(function() {
    			self.animateClass('');
    		}, 1000);
    	},

    	onUpdate: function () {
    		this._super();
    		this.loadAnimate();
    	}
    })
});