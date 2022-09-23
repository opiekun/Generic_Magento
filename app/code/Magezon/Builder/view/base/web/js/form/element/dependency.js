define([
	'angular',
	'jquery'
], function(angular, $) {

	return {
		controller: function($scope, $rootScope, $timeout) {
			var initializing = true;

			$scope.$watch('model.' + $scope.options.key, function(value) {
				if (initializing) {
					$timeout(function() { initializing = false; });
				} else {
					if ($scope.to.values && $scope.to.values.hasOwnProperty(value)) {
						angular.forEach($scope.to.values[value], function(value, key) {
							$scope.model[key] = value;
						});
					}
				}
			});

			if ($scope.to.groupsConfig) {
				$scope.$watch('model.' + $scope.options.key, function(value) {
					$rootScope.$broadcast('showField', {
						value: $scope.model[$scope.options.key],
						groupsConfig: $scope.to.groupsConfig
					});
				});
			}

			if ($scope.to.target) {
				angular.forEach($scope.to.target, function(value, index) {
					$scope.$watch('model.' + value, function(value) {
						$rootScope.$broadcast('showField', {
							value: $scope.model[$scope.options.key],
							groupsConfig: $scope.to.groupsConfig
						});
					});
					$scope.$watchCollection('model.' + value, function(value) {
						$rootScope.$broadcast('showField', {
							value: $scope.model[$scope.options.key],
							groupsConfig: $scope.to.groupsConfig
						});
					});
				});
			}
		}
	}
});