define([
	'angular',
	'moment',
	'Magezon_Builder/js/countdown',
	'moment-timezone-with-data'
], function(angular, moment) {

	var directive = function(magezonBuilderUrl) {
		return {
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/builder/element/countdown.html');
			},
			controller: function($scope, $controller) {
				var parent = $controller('baseController', {$scope: $scope});
				angular.extend(this, parent);
			},
			link: function(scope, element) {
				var loadCountdown = function() {
					var _element = scope.element;
					var timeStr  = _element.year + '-' + _element.month + '-' + _element.day + ' ' + _element.hours + ':' + _element.minutes;
					var time     = moment(timeStr, 'YYYY-MM-DD HH:mm').tz(_element.time_zone);	
					if (element.data("countdown")) {
						element.data("countdown").clearInterval();
						element.find('.mgz-element-bar').css({
							'stroke-dashoffset': '',
							'width': ''
						});
						element.countdown('destroy');
					}
					var time1 = time.isValid() ? time.format() : moment().format();
					element.countdown({
						type:_element.layout,
						time: time1
					});
				}
				scope.loadElement = function() {
					loadCountdown();
				}
			},
			controllerAs: 'mgz'
		}
	}

	return directive;
});