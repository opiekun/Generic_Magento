define([
	'jquery',
	'angular'
], function($, angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/navigator/element/default.html');
			},
			controller: function($rootScope, $scope, $controller) {
				var parent = $controller('baseController', {$scope: $scope});
				angular.extend(this, parent);
				var self = this;

				this.getWrapperClasses = function() {
					var element = $scope.element;
					var classes = [];
					classes.push('mgz-navigator-element');
					classes.push('mgz-navigator-element-' + element.id);
					if (element.disable_element) classes.push('mgz-element-disabled');
					if (element.builder.actived) classes.push('mgz-element-actived');
					if (element.hidden_default) classes.push('mgz-element-hide-default');
					if (element.disable_element) classes.push('mgz-element-disabled');
					return classes;
				}

				this.getStype = function() {
					var level = self.getParents().length;
					var paddingLeft = ((level + 1) * 1.6) + 'rem';
					if (!$scope.element.builder.is_collection) {
						paddingLeft = ((level + 1) * 1.8) + 'rem';
					}
					return {
						'padding-left': paddingLeft
					}
				}

				$scope.toggleElementList = function() {
					$scope.element.builder.navigator.listVisible = !$scope.element.builder.navigator.listVisible;
				}

				$scope.$on('addNewElment', function(event, item) {
					if ((item.action == 'before' || item.action == 'after' || item.action == 'replace')) {
						if ($scope.isChildren(item.elem)) {
							$scope.listVisible = true;
						}
					}
					if (item.action == 'append' && item.elem.id == $scope.element.id) {
						$scope.listVisible = true;
					}
				});

				var loaded = false;
				$scope.$watch('element.builder.name', function(name) {
					if (loaded) {
						$rootScope.$broadcast('exportShortcode');
					}
					loaded = true;
				});
			},
			controllerAs: 'mgz'
		}
	}

	return directive;
});