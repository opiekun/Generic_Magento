define([
	'jquery',
	'angular'
], function($, angular) {

	return {
		templateOptions: {
			labelProp: 'label',
			valueProp: 'value'
		},
		controller: function($rootScope, $scope, magezonBuilderService, elementManager, magezonBuilderModal, profileManager) {
			$scope.loadTemplates = function() {
				$rootScope.$broadcast('enableModalSpinner');
				$scope.to.loading = true;
				if ($scope.to.url) {
					$scope.to.loading = true;
					magezonBuilderService.post($scope.to.url, {
						field: $scope.options.key,
						url: $rootScope.builderConfig.profile.templateUrl
					}, true, function(res) {
						$scope.$apply(function() {
							angular.forEach(res, function(item, key) {
								if (item.profile) {
									var profile = profileManager.prepareProfile(item.profile, true);
									item.elements = profile.elements;
								}
								if (item.label) item.name = item.label;
							});
							$scope.to.options = res;
							$scope.to.loading = false;
							$rootScope.$broadcast('disableModalSpinner');
						});
					});
				} else if ($scope.to.source) {
					$scope.to.loading = true;
					magezonBuilderService.post('mgzbuilder/ajax/itemList', {
						type: $scope.to.source,
						url: $rootScope.builderConfig.profile.templateUrl
					}, true, function(res) {
						$scope.$apply(function() {
							angular.forEach(res, function(item, key) {
								if (item.profile) {
									var profile = profileManager.prepareProfile(item.profile, true);
									item.elements = profile.elements;
								}
								if (item.label) item.name = item.label;
							});
							$scope.to.options = res;
							$scope.to.loading = false;
							$rootScope.$broadcast('disableModalSpinner');
						});
					});
				}
			}
			$scope.loadTemplates();

			$scope.$on('loadTemplates', function() {
				$scope.loadTemplates();
			});

			$scope.openElements = function(item) {
				item['active'] = !item['active'];
			}

		    $scope.importItem = function(item) {
		    	if (item.file || item.elements.length) {
			    	var elements;
			    	if (item.elements && item.elements.length) {
			    		elements = angular.copy(elementManager.prepareElements(item.elements, true));
						angular.forEach(elements, function(elem, index) {
							$rootScope.profile.elements.push(angular.copy(elem));
						});
						magezonBuilderModal.closeModal();
						$rootScope.$broadcast('addHistory', {
							type: 'imported_template',
							title: item.name
						});
						item.importing = false;
			    	}
			    	if (item.file) {
			    		item.importing = true;
			    		magezonBuilderService.post('mgzbuilder/ajax/template', {
							item: item
						}, true, function(res) {
							elements = elementManager.prepareElements(res.elements, true);
							angular.forEach(elements, function(elem, index) {
								$rootScope.profile.elements.push(angular.copy(elem));
							});
							magezonBuilderModal.closeModal();
							$rootScope.$broadcast('addHistory', {
								type: 'imported_template',
								title: item.name
							});
							$scope.$apply(function() {
								item.importing = false;
							});
						});
			    	}
				}
		    }
		}
	}
})