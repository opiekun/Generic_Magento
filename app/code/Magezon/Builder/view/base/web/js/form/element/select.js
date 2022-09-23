define([
	'angular'
], function(angular) {

	return {
		templateOptions: {
			labelProp: 'label',
			valueProp: 'value'
		},
		controller: function($scope, $timeout, magezonBuilderService) {
			$scope.to.loading = false;
			if ($scope.to.lazyOptions) {
				$scope.to.options = eval($scope.to.lazyOptions);
			}
			if ($scope.to.builderConfig) {
				$scope.to.loading = true;
				magezonBuilderService.getBuilderConfig($scope.to.builderConfig, function(options) {
					$timeout(function() {
						$scope.to.options = options;
						$scope.to.loading = false;
					});
				});
			}

			if ($scope.to.url) {
				$scope.to.loading = true;
				magezonBuilderService.post($scope.to.url, {
					field: $scope.options.key
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
					field: $scope.options.key
				}, true, function(res) {
					$scope.$apply(function() {
						$scope.to.options = res;
						$scope.to.loading = false;
					});
				});
			}
		}
	}
})