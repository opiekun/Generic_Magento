define([
	'jquery',
	'angular',
], function($, angular) {

	var heading = function(magezonBuilderUrl, $compile, $timeout) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/builder/element/heading.html');
			},
			controller: function($scope, $controller) {
				var parent = $controller('baseController', {$scope: $scope});
				angular.extend(this, parent);
				var self = this;
			},
			link: function($scope, $element, $attr) {
				var $parent = $element.parent();
				$scope.$watch('element.heading_type', function(newVal) {
					var template = '<' + newVal + ' class="mgz-element-heading-text" content-editable="true" type="multiple" ng-model="element.text" ng-model-options="{ debounce: 300 }" data-placeholder="Edit Heading Text">' + $scope.element.text + '</' + newVal + '>';
					var html = $compile(template)($scope);
                    $parent.html(html);
                    if ($scope.loaded) {
                    	$parent.children('.mgz-contenteditable-element').trigger('focus');	
                    }
				});
				$timeout(function() {
					$scope.loaded = true;
				}, 500);
			},
			controllerAs: 'mgz'
		}
	}

	return heading;
});