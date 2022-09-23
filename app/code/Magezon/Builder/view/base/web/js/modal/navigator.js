define([
	'jquery',
	'angular'
], function($, angular) {

	return {
		templateUrl: 'Magezon_Builder/js/templates/modal/navigator.html',
		controller: function(
			$rootScope, 
			$scope, 
			$uibModalInstance, 
			$controller,
			form, 
			modal
		) {
			var parent = $controller('modalBaseController', {$scope: $scope, $uibModalInstance: $uibModalInstance, modal: modal, form: form});
			angular.extend(this, parent);

			$scope.listVisible = $rootScope.builderConfig.navigatorListVisible;
			$scope.expandAll = function() {
				$scope.listVisible = !$scope.listVisible;
				$rootScope.builderConfig.navigatorListVisible = $scope.listVisible;
				var parents = {};
				var activeListControl = function(elements, status) {
					angular.forEach(elements, function(element) {
						element.builder.navigator.listVisible = status;
						if (element.elements) {
							activeListControl(element.elements, status);
						}
					});
				}
				activeListControl($scope.profile.elements, $scope.listVisible);
			}
		}
	}
});