define([
	'jquery',
	'angular'
], function($, angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/modal/button/replace.html');
			},
			controller: function($rootScope, $scope, elementManager) {

				$scope.replace = function() {
					$rootScope.$broadcast('addElement', {elem: $scope.$parent.mgz.element, action: 'replace' });
				}

				$scope.canReplace = function() {
					return elementManager.canReplace($scope.$parent.mgz.element);
				}
			}
		}
	}

	return directive;
});