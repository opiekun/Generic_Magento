define([
	'jquery',
	'mgzspectrum',
    'mgztinycolor',
    'Magezon_Builder/js/ui/form/element/color-picker-palette'
], function($, spectrum, tinycolor, palette) {

	return {
		controller: function($scope, $timeout) {

			$scope.value = $scope.model[$scope.options.key];
			var element = $('#' + $scope.id);

			var getColor = function(color) {
				if (!color) color = $('#' + $scope.id).spectrum('get').toString();
				return $scope.to.hex ? tinycolor(color).toHexString() : tinycolor(color).toString();
			}

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
                showAlpha: $scope.to.hex ? false : true,
                showSelectionPalette: true,
                preferredFormat: 'hex',
                color: $scope.value,
                hide : function(c) {
            		$timeout(function() {
                		if ($('#' + $scope.id).spectrum('get')) {
                			$scope.model[$scope.options.key] = $scope.value = getColor();
                			$('#' + $scope.id).spectrum('set', $scope.value);
                		} else {
                			$scope.model[$scope.options.key] = $scope.value = '';
                		}
                	});
                }
            };
            config = angular.extend(config, $scope.to.colorPickerConfig);

			$timeout(function() {
				$('#' + $scope.id).spectrum(config);
				$('#' + $scope.id + '-input').change(function(event) {
					var value = $(this).val();
					if (value) {
                        if (tinycolor(value).isValid()) {
    						value = getColor(value);
    						$('#' + $scope.id).spectrum('set', value);
    						$scope.model[$scope.options.key] = $scope.value = getColor();
    						$(this).val(value);
                        }
					} else {
                        $scope.model[$scope.options.key] = '';
                    }
				});
			}, 200);
		}
	}
});