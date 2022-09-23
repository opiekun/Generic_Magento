define([
	'jquery',
	'angular'
], function($, angular) {

	var baseCtrl = function(
		$scope,
		$timeout
	) {
		var self = this;
		$scope.headingTypes = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
		$scope.alignTypes   = ['left', 'center', 'right'];

		$scope.colorPickerConfig = {
			appendTo: "parent"
		}

		$scope.changeToolbar = function(e, value, key) {
			if (angular.isFunction($scope.updatedkey)) {
				$scope.updatedkey(key, value);
			}
			$('.' + $scope.element.id).find('.mgz-contenteditable-element').trigger('focus');
			e.stopPropagation();
		}

		$scope.outsideClick = function() {
			$scope.element.builder.editing = false;
		}
		
		return angular.copy(this);
	};

	return baseCtrl;
});