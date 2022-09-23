define([
	'angular',
	'jquery'
], function(angular, $) {

	return {
		templateOptions: {
			labelProp: 'label',
			valueProp: 'value'
		},
		controller: function($scope, $timeout, magezonBuilderService) {
			$scope.to.loading = false;

			if ($scope.to.builderConfig) {
				$scope.to.loading = true;
				magezonBuilderService.getBuilderConfig($scope.to.builderConfig, function(options) {
					$timeout(function() {
						$scope.to.options = options;
						$scope.to.loading = false;
					});
				});
			}

			$scope.refresh = function($select) {
				var search = $select.search ? $select.search : $scope.model[$scope.options.key];
				if (search && (search.length >= 2 || !isNaN(search))) {
					if ($scope.to.url) {
						$scope.to.loading = true;
						magezonBuilderService.post($scope.to.url, {
							field: $scope.options.key,
							q: search
						}, true, function(res) {
							$scope.$apply(function() {
								$scope.to.options = res;
								$scope.to.loading = false;
							});
						});
					} else if ($scope.to.source) {
						$scope.to.loading = true;
						magezonBuilderService.post('mgzbuilder/ajax/itemList', {
							type: $scope.to.source,
							field: $scope.options.key,
							q: search
						}, true, function(res) {
							$scope.$apply(function() {
								$scope.to.options = res;
								$scope.to.loading = false;
							});
						});
					}
				}
			}
		}
	}
})