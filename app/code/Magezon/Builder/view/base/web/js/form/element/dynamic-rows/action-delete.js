define([
	'jquery',
	'angular'
], function($, angular) {

	return {
		link: function(scope, element, attrs) {
			scope.removeItem = function() {
				scope.$emit('removeDynamicItem', scope.model);
			}
		}
	}
});