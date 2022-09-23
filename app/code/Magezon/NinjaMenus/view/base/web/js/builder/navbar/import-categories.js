define([
	'angular'
], function(angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_NinjaMenus/js/templates/builder/navbar/import-categories.html')
			},
			controller: function($scope, magezonBuilderModal) {
				$scope.openImportModal = function() {
					magezonBuilderModal.open('importCategories');
				}
			}
		}
	}

	return directive;
});