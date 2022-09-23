define([
	'angular'
], function(angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/builder/navbar/history.html')
			},
			controller: function($rootScope, $scope, magezonBuilderModal) {
				$scope.$on('openHistoryModal', function() {
					$scope.openModal();
				});
				$scope.openModal = function() {
					magezonBuilderModal.open('history');
				}
			}
		}
	}

	return directive;
});