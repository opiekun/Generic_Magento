define([
	'angular',
    'jquery'
], function(angular, $) {

    return {
        controller: function($scope) {

			$scope.tabs   = $scope.to.children;
			$scope.length = $scope.tabs.length;

			// Copy from forly.js
        	function formlyEval(scope, expression, $modelValue, $viewValue, extraLocals) {
        		if (angular.isFunction(expression)) {
        			return expression($viewValue, $modelValue, scope, extraLocals);
        		} else {
        			return scope.$eval(expression, angular.extend({ $viewValue: $viewValue, $modelValue: $modelValue }, extraLocals));
        		}
        	}

        	// Copy from forly.js
        	function getFormlyFieldLikeLocals(field, index) {
        		return {
        			model: field.model,
        			options: field,
        			index: index,
        			formState: $scope.options.formState,
        			originalModel: $scope.model,
        			formOptions: $scope.options,
        			formId: $scope.formId
        		};
        	}

        	// Copy from forly.js
        	function evalCloseToFormlyExpression(expression, val, field, index) {
        		var extraLocals = arguments.length <= 4 || arguments[4] === undefined ? {} : arguments[4];
        		extraLocals = angular.extend(getFormlyFieldLikeLocals(field, index), extraLocals);
        		return formlyEval($scope, expression, val, val, extraLocals);
        	}

			$scope.$watch('model', function() {
				var length = $scope.tabs.length;
				angular.forEach($scope.tabs, function (field, index) {
					// Copy from forly.js
					var model = field.model || $scope.model;
					var promise = field.runExpressions && field.runExpressions();
					if (field.hideExpression) {
						var val = model[field.key];
						field.hide = evalCloseToFormlyExpression(field.hideExpression, val, field, index, { model: model });
						if (field.hide) {
							length--;
						}
					}
				});
				$scope.length = length;
			}, true);
		}
	}
});