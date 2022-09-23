define([
	'angular'
], function (angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			scope: {
				element: '='
			},
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/navigator_item.html')
			}
		}
	}

	return directive;
});