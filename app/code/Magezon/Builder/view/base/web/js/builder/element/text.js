define([
	'jquery',
	'angular',
], function($, angular) {

	var text = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/builder/element/text.html');
			},
			controller: function($rootScope, $scope, $controller) {
				var loaded = false;
				var parent = $controller('baseController', {$scope: $scope});
				angular.extend(this, parent);

				$scope.onChange = function(value) {
					$rootScope.$broadcast('addHistory', {
						type: 'edited',
						title: $scope.element.builder.name,
						subtitle: 'Content'
					});
				}
			},
			controllerAs: 'mgz'
		}
	}

	return text;
});