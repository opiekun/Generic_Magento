define([
	'jquery',
	'angular'
], function($, angular) {

	return {
		templateUrl: 'Magezon_Builder/js/templates/modal/clear-layout.html',
		controller: function(
			$rootScope, 
			$scope, 
			$uibModalInstance, 
			$controller,
			form, 
			modal
		) {
			var parent = $controller('modalBaseController', {$scope: $scope, $uibModalInstance: $uibModalInstance, modal: modal, form: form});
			angular.extend(this, parent);

			var self = this;

			self.onSubmit = function() {
				$rootScope.profile.elements = [];
				$rootScope.$broadcast('addHistory', {
					type: 'cleared_layout'
				});
				$uibModalInstance.close(self.model);
			}
		}
	}
});