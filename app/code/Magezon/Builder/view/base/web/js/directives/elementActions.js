define([
	'jquery',
	'angular'
], function ($, angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getViewFileUrl('Magezon_Builder/js/templates/directives/element-actions.html');
			}
		}
	}

	return directive;
});