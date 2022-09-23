define([
	'jquery'
], function($) {

	return {
		controller: function($scope) {
			$scope.toggleHide = function(type) {
				$scope.model[type + '_hide'] = !$scope.model[type + '_hide'];
			}
		}
	}
});