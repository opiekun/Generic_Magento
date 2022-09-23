define([
	'jquery',
	'angular'
], function($, angular) {

	return {
		templateUrl: 'Magezon_Builder/js/templates/modal/form.html',
		controller: function(
			$scope, 
			$uibModalInstance, 
			$controller,
			form, 
			modal,
			magezonBuilderForm
		) {
			var parent = $controller('modalBaseController', {$scope: $scope, $uibModalInstance: $uibModalInstance, modal: modal, form: form});
			angular.extend(this, parent);
			
			var self = this;

			$scope.$emit('enableModalSpinner');
			magezonBuilderForm.getForm('modals.templates', function(tabs) {
				self.tabs = tabs;
				$scope.$emit('disableModalSpinner');
			});
		}
	}
});