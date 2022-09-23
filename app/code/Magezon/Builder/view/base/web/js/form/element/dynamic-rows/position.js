define([
	'jquery',
	'angular'
], function($, angular) {

	return {
		link: function(scope, element, attrs, ctrl) {
			$(element).find('input').change(function(event) {
				scope.$emit('sortDynamicItems', {
					key: scope.options.key
				});
			});
		}
	}
});