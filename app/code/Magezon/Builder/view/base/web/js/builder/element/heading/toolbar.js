define([
	'jquery',
	'angular'
], function(angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getViewFileUrl('Magezon_Builder/js/templates/builder/element/heading/toolbar.html');
			},
			controller: function($scope, $controller) {

				var parent = $controller('toolbarController', {$scope: $scope});
				angular.extend(this, parent);
				var self = this;

				$scope.fontSize = $scope.element.font_size;

				$scope.updatedkey = function(key, value) {
					switch(key) {
						case 'elemHeading':
							$scope.element.heading_type = value;
							$scope.addHistory('edited', 'Type');
						break;

						case 'elemAlign':
							$scope.element.align = value;
							$scope.addHistory('edited', 'Alignment');
						break;
					}
				}

				$scope.$watch('element.text', function(text, old) {
					if ($scope.isLoaded() && (text !== old)) {
						$scope.addHistory('edited', 'Text');
					}
				});

				$scope.$watch('element.color', function(color, old) {
					if ($scope.isLoaded() && (color !== old)) {
						$scope.addHistory('edited', 'Color');
					}
				});

				$scope.changeFontSize = function(e) {
					$scope.element.font_size = $scope.fontSize;
					$scope.addHistory('edited', 'Font Size');
				}
			}
		}
	}

	return directive;
});