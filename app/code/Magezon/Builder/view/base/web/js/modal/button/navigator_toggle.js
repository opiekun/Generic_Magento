define([
	'jquery',
	'angular'
], function($, angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/modal/button/navigator_toggle.html');
			},
			controller: function($rootScope, $scope) {
				$scope.listVisible = $rootScope.builderConfig.navigatorListVisible;
				$scope.toggle = function() {
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
					activeListControl($rootScope.profile.elements, $scope.listVisible);
				}
			}
		}
	}

	return directive;
});