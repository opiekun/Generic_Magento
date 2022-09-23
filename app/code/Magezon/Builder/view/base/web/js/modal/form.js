define([
	'jquery',
	'angular'
], function($, angular) {

	return {
		templateUrl: 'Magezon_Builder/js/templates/modal/form.html',
		controller: function(
			$rootScope, 
			$scope, 
			$uibModalInstance, 
			$controller,
			form,
			modal,
			magezonBuilderForm
		) {
			var parent = $controller('modalBaseController', {$scope: $scope, $uibModalInstance: $uibModalInstance, modal: modal, form: form});
			angular.extend(this, parent);

			this.model = form.model;
			var self = this;

			$scope.$emit('enableModalSpinner');
			magezonBuilderForm.getForm('modals.' + modal.key, function(tabs) {
				self.tabs = tabs;
				$scope.$emit('disableModalSpinner');
			});

			self.onSubmit = function() {
				$rootScope.$broadcast('modal_' + modal.key + '_saved');
				$uibModalInstance.close(self.model);
			}
		}
	}
});