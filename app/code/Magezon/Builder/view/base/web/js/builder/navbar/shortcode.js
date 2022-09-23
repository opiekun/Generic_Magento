define([
	'angular'
], function(angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/builder/navbar/shortcode.html')
			},
			controller: function($scope, magezonBuilderModal) {
				$scope.openProfileShortcodeModal = function() {
					magezonBuilderModal.open('profileShortcode');
				}
			}
		}
	}

	return directive;
});