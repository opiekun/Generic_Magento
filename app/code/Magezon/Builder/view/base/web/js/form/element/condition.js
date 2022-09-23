define([
	'jquery'
], function($) {

	$(document).on('click', '.rule-param-remove', function() {
		$('.rule-tree').trigger('change');
	});

	return {
		controller: function($rootScope, $scope, $sce, magezonBuilderService) {
			$scope.to.loading = true;
			$scope.id         = magezonBuilderService.uniqueid();
			$scope.status     = true;

			$scope.loadConditionsValue = function() {
				if ($scope.status) {
					$scope.status = false;
					$rootScope.$broadcast('disableSaveBtn');
					magezonBuilderService.post('mgzbuilder/ajax/conditionsValue', {
						values: $('.mgz-modal-form form').serialize(),
						field: $scope.options.key
					}, true, function(res) {
						if (res.status) {
							$scope.$apply(function() {
								$scope.model[$scope.options.key] = res.value;
							});
						}
						$rootScope.$broadcast('enableSaveBtn');
						$scope.status = true;
					});
				}
			}

			magezonBuilderService.post('mgzbuilder/ajax/conditions', {
				conditions: $scope.model[$scope.options.key],
				id: $scope.id
			}, true, function(res) {
				if (res.message) {
					alert(res.message);
				}
				if (res.status) {
					$scope.$apply(function() {
						$scope.html       = $sce.trustAsHtml(res.html);
						$scope.to.loading = false;

						$('#' + $scope.id).on('click', '.rule-param-remove,.rule-param-apply,.data-grid ._clickable,#select_widget_type', function() {
							$scope.loadConditionsValue();
						});

						$('#' + $scope.id).on('change', '.element-value-changer,.rule-tree', function() {
							$scope.loadConditionsValue();
						});
					});
				}
			});
		}
	}
});