define([
	'jquery'
], function($) {

	return {
		controller: function($scope, $timeout) {
			$timeout(function() {
				$scope.refreshEditor = true;
			}, 200);
		}
	}
});