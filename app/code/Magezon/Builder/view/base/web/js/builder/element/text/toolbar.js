define([
	'angular'
], function(angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			controller: function($scope, $controller) {
				var parent = $controller('toolbarController', {$scope: $scope});
				angular.extend(this, parent);
				var self = this;
				var loaded = false;

				$scope.$watch('element.content', function(content) {
					if (loaded) {
						$rootScope.$broadcast('addHistory', {
							type: 'edited',
							title: $scope.element.builder.name,
							subtitle: 'Content'
						});
					}
					loaded = true;
				});
			}
		}
	}

	return directive;
});