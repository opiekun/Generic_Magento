define([
	'jquery',
	'angular'
], function ($, angular) {

	var directive = function(magezonBuilderService, magezonBuilderUrl) {
		return {
			scope: {
				element: '='
			},
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/builder/element/profile.html');
			},
			controller: function($scope, $rootScope, $controller, elementManager) {
				//$scope.element = $scope.profile;
				$scope.element.builder = elementManager.getBuilderConfig('profile');
				//$scope.element.elements = [];
				var self = this;
				var parent = $controller('listController', {$scope: $scope});
				angular.extend(this, parent);

				var mainElement = $rootScope.builderConfig.mainElement;

				$scope.$on('addRootRowElement', function(event, columns) {
					var row            = self.addElement(mainElement, null, false);
					var builderElement = self.getBuilderElement(mainElement);
					var viewMode       = magezonBuilderService.getViewMode();
					var types          = columns.split('_');
					for (var i = 0; i < types.length - 1; i++) {
						var _type   = types[i].split('');
						var element = self.addElement(builderElement.children, row);
						var width;
						if (_type[1]==5) {
							width = 15
						} else {
							width = 12 * _type[0] / _type[1];
						}
						element['xl_size'] = '';
						element['lg_size'] = '';
						element['md_size'] = width;
						element['sm_size'] = '';
						element['xs_size'] = '';
					}

					// First Column
					row['elements'][0]['xl_size'] = '';
					row['elements'][0]['lg_size'] = '';
					row['elements'][0]['md_size'] = width;
					row['elements'][0]['sm_size'] = '';
					row['elements'][0]['xs_size'] = '';

					if (!$('.mgz-fullscreen').length) {
						$rootScope.$broadcast('setViewMode', 'xl');	
					}

					$rootScope.$broadcast('addHistory', {
						type: 'changed',
						subtitle: 'Row Layout'
					});
				});
			},
			controllerAs: 'mgz'
		}
	}

	return directive;
});