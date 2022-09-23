define([
	'jquery'
], function($) {

	return {
		expressionProperties: {
			'templateOptions.disabled': function(viewValue, modelValue, scope) {
				if (viewValue) {
					var prefix = scope.to.prefix;
					$('.mgz-design-layout').addClass('mgz-design-simply');
					scope.model[prefix + 'border_top_left_radius'] = scope.model[prefix + 'border_bottom_right_radius'] = scope.model[prefix + 'border_bottom_left_radius'] = scope.model[prefix + 'border_top_right_radius'];
					scope.model[prefix + 'margin_right'] = scope.model[prefix + 'margin_bottom'] = scope.model[prefix + 'margin_left'] = scope.model[prefix + 'margin_top'];
					scope.model[prefix + 'border_right_width'] = scope.model[prefix + 'border_bottom_width'] = scope.model[prefix + 'border_left_width'] = scope.model[prefix + 'border_top_width'];
					scope.model[prefix + 'padding_right'] = scope.model[prefix + 'padding_bottom'] = scope.model[prefix + 'padding_left'] = scope.model[prefix + 'padding_top'];
				} else {
					$('.mgz-design-layout').removeClass('mgz-design-simply');
				}
			}
		}
	}
})