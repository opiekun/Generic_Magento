define([
	'jquery',
	'angular'
], function($, angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/modal/button/save.html');
			},
			controller: function($scope) {
				$scope.disabled = false;

				$scope.$on('disableSaveBtn', function() {
					$scope.disabled = true;
				});

				$scope.$on('enableSaveBtn', function() {
					$scope.disabled = false;
				});
			}
		}
	}

	return directive;
});