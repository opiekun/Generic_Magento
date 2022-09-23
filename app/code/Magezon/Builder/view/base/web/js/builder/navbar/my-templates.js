define([
	'angular'
], function(angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/builder/navbar/my_templates.html')
			},
			controller: function($scope, magezonBuilderModal) {
				$scope.openTemplatesModal = function() {
					magezonBuilderModal.open('templates');
				}
			}
		}
	}

	return directive;
});