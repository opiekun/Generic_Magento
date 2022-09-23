define([
	'angular'
], function (angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			scope: {
				element: '=element'
			},
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/modal/elements_element.html')
			}
		}
	}

	return directive;
});