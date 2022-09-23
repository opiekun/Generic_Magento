define([
	'jquery',
	'angular',
], function($, angular) {

	var text = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getViewFileUrl('Magezon_Builder/js/templates/builder/element/list.html');
			},
			controller: function($scope, $controller) {
				var loaded = false;
				var parent = $controller('listController', {$scope: $scope});
				angular.extend(this, parent);
				var parentElement = $scope.$parent.$parent.element;
				this.getWrapperClasses = function() {
					var classes = parent.getWrapperClasses();
					var gapType = parentElement.gap_type ? parentElement.gap_type : 'padding';
					if (gapType == 'margin') {
						classes.push('mgz-row-gap-margin');
					}
					return classes;
				}
			},
			controllerAs: 'mgz'
		}
	}

	return text;
});