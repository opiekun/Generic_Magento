define([
	'jquery',
	'angular'
], function ($, angular) {

	var directive = function($timeout) {
		return {
			replace: true,
			restrict: 'A',
	        terminal: true,
	        transclude: false,
	        link: function (scope, element, attrs) {
	            $timeout(function() {
	            	scope.$eval(attrs.afterRender);
	            }, 100);
	        }
		}
	}

	return directive;
});