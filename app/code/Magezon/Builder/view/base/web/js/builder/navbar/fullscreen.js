define([
	'angular'
], function(angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/builder/navbar/fullscreen.html')
			},
			controller: function($rootScope, $scope) {
				$scope.toggleFullscreen = function() {
					$rootScope.fullscreen = !$rootScope.fullscreen;
					if (!$rootScope.fullscreen) {
						$rootScope.$broadcast('setViewMode', 'xl');
					}
				}
			}
		}
	}

	return directive;
});