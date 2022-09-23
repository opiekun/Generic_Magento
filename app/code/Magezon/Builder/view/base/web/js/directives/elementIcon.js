define([
	'jquery',
	'angular'
], function ($, angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			scope: {
				element: '='
			},
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/builder/element_icon.html');
			}
		}
	}

	return directive;
});