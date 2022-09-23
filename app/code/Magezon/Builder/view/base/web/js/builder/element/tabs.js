define([
	'angular'
], function(angular) {

	var directive = function(magezonBuilderUrl, $timeout) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getViewFileUrl('Magezon_Builder/js/templates/builder/element/tabs.html');
			},
			controller: function($scope, $controller) {

				var parent = $controller('listController', {$scope: $scope});
				angular.extend(this, parent);
				var self = this;

				$scope.element.builder.dndDisabled = true;

				angular.forEach($scope.element.elements, function(element, index) {
					if (index == 0) {
						element.builder.visible = true;
					} else {
						element.builder.visible = false;
					}
				});

				$scope.$on('afterAddElement', function(e, elem) {
					$scope.activeEventElement(elem);
				});

				$scope.$on('afterCloneElement', function(e, elem) {
					if ($scope.isChildren(elem)) {
						$scope.activeEventElement(elem);
					}
				});

				$scope.$on('afterDropElement', function(e, elem) {
					if ($scope.isChildren(elem)) {
						$scope.activeEventElement(elem);
					}
				});

				$scope.activeEventElement = function(elem) {
					if ($scope.isChildren(elem)) {
						$scope.activeElement(elem);
					}
				}

				$scope.$on('beforeRemoveElement', function(e, elem) {
					if ($scope.isChildren(elem)) {
						var index = $scope.getElemIndex(elem);
						var elem2 = $scope.element.elements[index+1] ? $scope.element.elements[index+1] : 0;
						if(elem2) $scope.activeElement(elem2);
					}
				});

				$scope.activeElement = function(element) {
					if (!element) return;
					element.builder.additionalClasses.push('mgz-tabs-tab-content');
					angular.forEach($scope.element.elements, function(el, index) {
						if (el !== element) {
							el.builder.visible = false;
						}
					});
					element.builder.visible = true;
					element.builder.additionalClasses.push('mgz-active');
				}

				$scope.activeFirstElement = function() {
					var active = true;
					angular.forEach($scope.element.elements, function(element) {
						if (!element.builder.visible) {
							active = false;
						}
					});
					if (!active) {
						$scope.activeElement($scope.element.elements[0]);
					}
				}
				$scope.activeFirstElement();

				$scope.getClassess = function() {
					var _elem = $scope.element;
					var clasess = [];
					clasess.push('mgz-tabs');
					clasess.push('mgz-tabs-' + _elem.id);
					clasess.push('mgz-element-tab-align-' + _elem.tab_align);
					clasess.push('mgz-element-tab-position-' + _elem.tab_position);
					if (_elem.no_fill_content_area) clasess.push('mgz-tabs-no-fill-content');
					if (_elem.mobile_accordion) clasess.push('tabs-mobile-accordion');
					return clasess;
				}

				$scope.getSpacing = function() {
					return $scope.element.spacing ? parseFloat($scope.element.spacing) : 0;
				}

				$scope.getGap = function() {
					return $scope.element.gap ? parseFloat($scope.element.gap) : 0;
				}
			},
			controllerAs: 'mgz'
		}
	}

	return directive;
});