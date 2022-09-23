define([
	'jquery',
	'angular'
], function($, angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/modal/button/minimize.html');
			},
			controller: function($scope) {
				$scope.minimized = false;
				$scope.minimize = function() {
					var modalSelector = $scope.$parent.mgz.getModalSelector();
					var headerHeight  = $scope.$parent.mgz.getHeaderHeight();
					modalSelector.stop();
					if ($scope.minimized) {
						var height = parseFloat(modalSelector.css('max-height'));
						if (height) {
							modalSelector.animate({"height": height}, "medium", function() {
								modalSelector.css('height', '');
							});
						} else {
							modalSelector.css('height', '');
							$scope.$parent.mgz.resizeInner();
						}
						modalSelector.removeClass('mgz-minimized');
					} else {
						modalSelector.animate({"height": headerHeight}, "medium");
						modalSelector.addClass('mgz-minimized');
					}
					$scope.minimized = !$scope.minimized;
				}
			}
		}
	}

	return directive;
});