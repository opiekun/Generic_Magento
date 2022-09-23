define([
	'jquery',
	'mage/calendar'
], function($) {

	return {
		link: function(scope, element) {
			setTimeout(function() {
				$('#' + scope.id).calendar({
					dateFormat: "mm/dd/yy"
				});
			}, 100);
		}
	}
});