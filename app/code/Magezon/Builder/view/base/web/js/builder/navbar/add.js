define([
	'angular'
], function(angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/builder/navbar/add.html')
			},
			controller: function($rootScope, $scope) {
				$scope.openElementsModal = function() {
					$rootScope.$broadcast('openElementsModal', true);
				}
			}
		}
	}

	return directive;
});