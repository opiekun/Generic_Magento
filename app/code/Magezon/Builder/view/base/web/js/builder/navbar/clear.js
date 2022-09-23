define([
	'jquery',
	'angular'
], function($, angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/builder/navbar/clear.html')
			},
			controller: function($rootScope, $scope, magezonBuilderModal) {
				$scope.openModal = function(e) {
					magezonBuilderModal.open('clear_layout');
				}
			}
		}
	}

	return directive;
});