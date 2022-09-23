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
			$timeout,
			form,
			modal,
			profileManager,
			magezonBuilderForm,
			elementManager
		) {
			var parent = $controller('modalBaseController', {$scope: $scope, $uibModalInstance: $uibModalInstance, modal: modal, form: form});
			angular.extend(this, parent);

			var self = this;
			var elements = angular.toJson(profileManager.getJsonElements());
			self.model = {
				shortcode: elements == '[]' ? '' : elements
			};

			$scope.$emit('enableModalSpinner');
			magezonBuilderForm.getForm('modals.profileShortcode', function(tabs) {
				self.tabs = tabs;
				$scope.$emit('disableModalSpinner');
			});

			self.onSubmit = function() {
				try {
					var shortcode = self.model.shortcode ? self.model.shortcode : [];
					var elements = elementManager.prepareElements(angular.fromJson(shortcode));
					profileManager.updateElements(elements);
					$timeout(function() {
						$uibModalInstance.dismiss('cancel');
					}, 100);
					$rootScope.$broadcast('addHistory', {
						type: 'edited',
						title: 'Profile Shortcode'
					});
				} catch (e) {
					alert('Invalid JSON string');
				}
			}
		}
	}
});