define([
	'jquery',
	'angular',
    'Magezon_Builder/js/ui/form/element/color-picker-palette'
], function ($, angular, palette) {

	var directive = function(magezonBuilderUrl, $timeout, $rootScope) {
		return {
			replace: true,
			require: "ngModel",
			scope: {
				config: '='
			},
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/directives/color.html');
			},
			link: function(scope, element, attrs, ngModel) {
				var initColorPicker = function() {
					var config = {
						containerClassName: 'mgz-spectrum',
		                chooseText: 'Apply',
		                cancelText: 'Cancel',
		                maxSelectionSize: 8,
		                clickoutFiresChange: true,
		                allowEmpty: true,
		                localStorageKey: 'magezon.spectrum',
		                palette: palette,
		                showInput: true,
		                showInitial: false,
		                showPalette: true,
		                showAlpha: true,
		                showSelectionPalette: true,
		                preferredFormat: 'hex',
		                color: ngModel.$viewValue,
		                hide : function(c) {
		                	$timeout(function() {
		                		if (c) ngModel.$setViewValue(c.toString());
		                	});
		                }
		            };
		            config = angular.extend(config, scope.config);
		            element.spectrum(config);
		        }
	            setTimeout(function() {
	            	initColorPicker();
	            });
			}
		}
	}

	return directive;
});