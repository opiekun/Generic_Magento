define([
	'angular',
	'underscore',
], function(angular, _) {

	return {
		controller: function($scope, magezonBuilderService, $timeout) {
			var initializing = true;
			$scope.to.options = [];

			$scope.$watch('model.type', function(type) {
				if (type !== 'custom') {
					$scope.to.label = magezonBuilderService.capitalize(type);
				}
				if (initializing) {
					$timeout(function() { initializing = false; });
				} else {
					$scope.model.id = '';
					$scope.to.options = [];
				}
			});

			$scope.$watch('model.id', function(id) {
				if (id) {
					var item = _.findWhere($scope.to.options, {value: id});
					if (!$scope.model.title) $scope.model.title = item.label;
				}
			});

			$scope.refresh = function($select) {
				var type = $scope.model.type;
				var search = $select.search ? $select.search : $scope.model[$scope.options.key];
				if (search && ($select.search.length >= 2 || $scope.model[$scope.options.key])) {
					$scope.to.loading = true;
					magezonBuilderService.post('mgzbuilder/ajax/itemList', {
						type: $scope.model.type,
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
})