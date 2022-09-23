define([
	'angular',
	'jquery'
], function(angular, $) {

	var directive = function(magezonBuilderUrl, magezonBuilderService, $compile) {
		return {
      		replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getViewFileUrl('Magezon_NinjaMenus/js/templates/builder/element/item.html');
			},
			controller: function($rootScope, $scope, $controller) {
				var parent = $controller('listController', {$scope: $scope});
				angular.extend(this, parent);
				var self = this;

				$scope.addBlock = false;
				$scope.added = false;

				var element = $scope.element;

				if (element.submenu_type=='mega') {
					$scope.submenuLoaded = false;
				} else {
					$scope.submenuLoaded = true;
				}

				var parentScope = $scope.$parent.$parent;
				if (parentScope.mgz.isProfile()) {
					$scope.submenuLoaded = false;
				}

				var submenuHtml = '<div ng-class="mgz.getInnerClasses()" ng-if="submenuLoaded&&element.elements.length" ng-include="::$root.magezonBuilderUrl.getViewFileUrl(\'Magezon_Builder/js/templates/builder/element/list.html\')"dnd-disable-builder="!element.builder.is_collection"dnd-disable-if="element.builder.dndDisabled" dnd-list="element.elements"dnd-drop="mgz.dropElement(item, index, element)"dnd-allowed-types="::element.builder.allowed_types"></div>';

				$scope.loadElement = function() {
					if (!$scope.added) {
						var html = $compile(submenuHtml)($scope);
						$scope.getEl().append(html);
						$scope.added = true;
					}

					if ($scope.element.item_type == 'category' && $scope.element.category_id && $scope.element.cat_name) {
						magezonBuilderService.elemPost($scope.element, 'mgzbuilder/ajax/itemInfo', {
							type: 'category',
							q: $scope.element.category_id
						}, true, function(res) {
							$scope.$apply(function() {
								$scope.element.title = res.label;
							})
						});
					}
				}

				self.getWrapperClasses = function() {
					var classes = parent.getWrapperClasses();
					classes.push('nav-item');

					if (element.elements) {
						classes.push(element.submenu_type);
						classes.push(element.submenu_position);
					}

					if (element.label) {
						classes.push('label-' + element.label_position);
					}

					if (parentScope.mgz.isProfile()) {
						classes.push('level0');
					}

					if ($scope.submenuLoaded && element.submenu_type == 'mega' && element.elements.length) {
						classes.push('ninjamenus-submenu-actived');
					}

					return classes;
				}

				self.getInnerClasses = function() {
					var classes = parent.getInnerClasses();
					classes.push('item-submenu');
					return classes;
				}

				$scope.activeElement = function(e) {
					if (element.submenu_type == 'mega') {
						if (parentScope.mgz.isProfile()) {
							$rootScope.$broadcast('activeMenuItem', element);
						} else {
							$scope.submenuLoaded = !$scope.submenuLoaded;
						}
					}
				}

				if (parentScope.mgz.isProfile()) {
					$scope.$on('activeMenuItem', function(e, elem) {
						if (elem.id == element.id) {
							$scope.submenuLoaded = !$scope.submenuLoaded;
						} else {
							$scope.submenuLoaded = false;
						}
					});
				}
			},
			link: function(scope, element, attrs) {
				$(element).hover(function() {
					scope.loaded = true;
				});
			},
			controllerAs: 'mgz'
		}
	}

	return directive;
});