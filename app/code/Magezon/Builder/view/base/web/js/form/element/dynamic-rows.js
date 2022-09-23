define([
	'angular'
], function(angular) {

	return {
		controller: function($scope) {
			$scope.formOptions = {formState: $scope.formState};
			$scope.addNew = addNew;

			var unique = 1;

			angular.forEach($scope.model[$scope.options.key], function(_item, index) {
				_item['position'] = index + 1;
			});

			$scope.copyFields = copyFields;

			$scope.$on('removeDynamicItem', function(event, item) {
				var index = $scope.model[$scope.options.key].indexOf(item);
				if (index !== -1) {
					$scope.model[$scope.options.key].splice(index, 1);
				}
			});

			$scope.$on('radioDefaultDynamicItem', function(event, data) {
				var index = $scope.model[$scope.options.key].indexOf(data.item);
				if (index !== -1) {
					angular.forEach($scope.model[$scope.options.key], function(_item, index) {
						if (_item===data.item) {
							_item[data.key] = 1;
						} else {
							_item[data.key] = 0;
						}
					});
				}
			});

			$scope.$on('sortDynamicItems', function(event, data) {
				$scope.model[$scope.options.key] = _.sortBy($scope.model[$scope.options.key], data.key);
			});

			function copyFields(fields) {
				fields = angular.copy(fields);
				addRandomIds(fields);
				return fields;
			}

			function addNew() {
				$scope.model[$scope.options.key] = $scope.model[$scope.options.key] || [];
				var repeatsection = $scope.model[$scope.options.key];
				var lastSection   = repeatsection[repeatsection.length - 1];
				var newsection    = {};
				if (lastSection) {
					newsection = angular.copy(lastSection);
				}
				var newsection = {};
				newsection['position'] = repeatsection.length + 1;
				repeatsection.push(newsection);
			}

			function addRandomIds(fields) {
				unique++;
				angular.forEach(fields, function(field, index) {
					if (field.fieldGroup) {
						addRandomIds(field.fieldGroup);
						return;
					}
					if (field.children) {
						addRandomIds(field.children);
						return;
					}
					if (field.templateOptions && field.templateOptions.children) {
						addRandomIds(field.templateOptions.children);
						return;
					}
					if (field.key) {
						field.id = field.key.replace(/\./g,'_') + '_' + index + '_' + unique + getRandomInt(0, 9999);
					}
				});
			}

			function getRandomInt(min, max) {
				return Math.floor(Math.random() * (max - min)) + min;
			}
		}
	}
})