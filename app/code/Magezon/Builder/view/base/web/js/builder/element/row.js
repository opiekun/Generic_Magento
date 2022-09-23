define([
	'jquery',
	'angular',
], function($, angular) {

	var text = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getViewFileUrl('Magezon_Builder/js/templates/builder/element/row.html');
			},
			controller: function($scope, $controller) {
				var loaded = false;
				var parent = $controller('listController', {$scope: $scope});
				angular.extend(this, parent);
				this.getWrapperClasses = function() {
					var classes = parent.getWrapperClasses();
					if ($scope.element.row_type == 'contained') {
						classes.push('mgz-container');
					}
					if ($scope.element.equal_height) {
						classes.push('mgz-row-equal-height');
						if ($scope.element.content_position) classes.push('content-' + $scope.element.content_position);
					}
					if ($scope.element.full_height) classes.push('mgz-row-full-height');
					return classes;
				}
			},
			link: function(scope, element) {
				var height;
				scope.$watch('element.full_height', function(fullHeight) {
					if (fullHeight) {
						height = $(window).height();
					} else {
						height = '';
					}
					element.parent().css('min-height', height);
				});
			},
			controllerAs: 'mgz'
		}
	}

	return text;
});