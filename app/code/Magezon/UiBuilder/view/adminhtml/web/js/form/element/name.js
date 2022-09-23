define([
    'jquery',
    'Magento_Ui/js/form/element/abstract'
], function ($, Abstract) {
    'use strict';

    return Abstract.extend({

        /**
         *  Callback when value is changed by user
         */
        userChanges: function () {
            this._super();

			var recordData     = this.builderApp().recordData();
			var currentElement = this.builderApp().currentElement;
			var value          = this.value();
			if (currentElement.recordId) {
				var recordInstance = _.find(recordData, function (data) {
	                return data['elem_name'] === value && currentElement.recordId !== data['record_id'];
	            });
	            if (recordInstance) {
	            	$('.uibuilder-modal .mfb-error-duplicate').show();
	            	$('#' + this.uid).parents('.admin__field').addClass('_error');
	        	} else {
	        		$('.uibuilder-modal .mfb-error-duplicate').hide();
	        		$('#' + this.uid).parents('.admin__field').removeClass('_error');
	        	}
	        }
        }
    });
});