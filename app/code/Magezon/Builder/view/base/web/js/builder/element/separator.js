define([
	'jquery',
	'angular',
], function($, angular) {

	var heading = function(magezonBuilderUrl, $compile) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/builder/element/separator.html');
			},
			controller: function($scope, $controller) {
				var parent = $controller('baseController', {$scope: $scope});
				angular.extend(this, parent);
				var self = this;
			},
			link: function($scope, $element, $attr) {
				var $parent = $element.parent();
				$scope.$watch('element.title_tag', function(newVal) {
					var template = '<' + newVal + ' class="title mgz-inline-edit">';
						if ($scope.element.add_icon && $scope.element.icon_position == 'left') {
							template += '<i class="mgz-icon-element ' + $scope.element.icon + '"></i>';
						}
						template += '<span content-editable="true" ng-model="element.title">' + $scope.element.title + '</span>';
						if ($scope.element.add_icon && $scope.element.icon_position == 'right') {
							template += '<i class="mgz-icon-element ' + $scope.element.icon + '"></i>';
						}
						template += '</' + newVal + '>';
						var html = $compile(template)($scope);
						$parent.find('.mgz-inline-edit').replaceWith(html);
				});
			},
			controllerAs: 'mgz'
		}
	}

	return heading;
});