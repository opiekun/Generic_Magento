define([
	'angular'
], function(angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/builder/navbar/settings.html')
			},
			controller: function($scope, magezonBuilderModal) {
				$scope.openModal = function () {
					magezonBuilderModal.open('settings');
		        }
			}
		}
	}

	return directive;
});