define([
	'angular'
], function(angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/builder/control/updown.html')
			},
			controller: function($rootScope, $scope, elementManager) {

				$scope.upClass   = 'mgz-fa-chevron-up';
				$scope.upTitle   = 'Move up';
				$scope.downClass = 'mgz-fa-chevron-down';
				$scope.downTitle = 'Move down';

				$scope.isUpVisible = function() {
					var el = elementManager.getEl($scope.element);
					if (!el.prev('.mgz-element').length || !el.prev('.mgz-element').is(':visible')) return false;
					var elPosition   = el.position();
					var nextPosition = el.prev('.mgz-element').position();
					if (elPosition.top == nextPosition.top) {
						$scope.upTitle = 'Move left';
						$scope.upClass = 'mgz-fa-chevron-left';
					}
					return true;
				}

				$scope.moveUp = function() {
					$rootScope.$broadcast('moveUpElement', $scope.element);
				}

				$scope.isDownVisible = function() {
					var el = elementManager.getEl($scope.element);
					if (!el.next('.mgz-element').length || !el.next('.mgz-element').is(':visible')) return false;
					var elPosition   = el.position();
					var prevPosition = el.next('.mgz-element').position();
					if (elPosition.top == prevPosition.top) {
						$scope.downTitle = 'Move right';
						$scope.downClass = 'mgz-fa-chevron-right';
					}
					return true;	
				}

				$scope.moveDown = function() {
					$rootScope.$broadcast('moveDownElement', $scope.element);	
				}
			}
		}
	}

	return directive;
});