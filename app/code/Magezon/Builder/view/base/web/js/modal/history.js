define([
	'jquery',
	'angular',
	'moment'
], function($, angular, moment) {

	return {
		templateUrl: 'Magezon_Builder/js/templates/modal/history.html',
		controller: function(
			$scope, 
			$uibModalInstance,
			$controller, 
			form,
			modal,
			historyManager
		) {
			var parent = $controller('modalBaseController', {$scope: $scope, $uibModalInstance: $uibModalInstance, modal: modal, form: form});
			angular.extend(this, parent);

			$scope.previewItem = function(item) {
				historyManager.previewItem(item);
			}

			$scope.applyItem = function(item) {
				historyManager.applyItem(item);
			}

			$scope.getDateTime = function(item) {
				return moment(item.time).fromNow();
			}

			$scope.getDateTimeTitle = function(item) {
				return moment(item.time).format('MM/DD/YYYY HH:mm:s');
			}

			var timer;
			setTimeout(function() {
				timer = setInterval(function() {
					$scope.$apply();
				}, 1000);
			}, 10000);

			$scope.$on('$destroy', function() {
				if (timer) {
					clearInterval(timer);
				}
			});
		}
	}
});