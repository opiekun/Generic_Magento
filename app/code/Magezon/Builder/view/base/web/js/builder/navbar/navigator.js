define([
	'angular'
], function(angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/builder/navbar/navigator.html')
			},
			controller: function($rootScope, $scope, magezonBuilderModal) {
				$scope.$on('openNavigatorModal', function() {
					$scope.openModal();
				});
				$scope.openModal = function() {
					magezonBuilderModal.open('navigator').result.then(function() {}, function() {
						if($rootScope.activedElement) $rootScope.activedElement.builder.actived = false;
					});
				}
			}
		}
	}

	return directive;
});