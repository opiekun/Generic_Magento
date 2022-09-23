define([
	'angular'
], function(angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getViewFileUrl('Magezon_Builder/js/templates/builder/element/separator/toolbar.html');
			},
			controller: function($scope, $controller) {
				var parent = $controller('toolbarController', {$scope: $scope});
				angular.extend(this, parent);
				var self = this;

				$scope.updatedkey = function(key, value) {
					switch(key) {
						case 'elemHeading':
							$scope.element.title_tag = value;
							$scope.addHistory('edited', 'Tag');
						break;

						case 'elemAlign':
							$scope.element.title_align = value;
							$scope.addHistory('edited', 'Alignment');
						break;
					}
				}

				$scope.$watch('element.text', function(text) {
					if ($scope.isLoaded()) {
						$scope.addHistory('edited', 'Text');
					}
				});

				$scope.$watch('element.color', function(color) {
					if ($scope.isLoaded()) {
						$scope.addHistory('edited', 'Color');
					}
				});
			}
		}
	}

	return directive;
});