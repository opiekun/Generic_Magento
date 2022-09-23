define([
	'jquery',
	'angular'
], function($, angular) {

	return {
		templateOptions: {
			labelProp: 'label',
			valueProp: 'value'
		},
		controller: function($rootScope, $scope, magezonBuilderService) {
			$scope.font  = $scope.to.defaultFont ? $scope.to.defaultFont : 'awesome';
			$scope.fonts = [];
			angular.forEach($rootScope.builderConfig.fonts, function(font) {
				$scope.fonts.push(font);
			});

			$scope.onSuccess = function(data) {
				$scope.to.loading = false;
				$scope.to.options = data;
			};

			$scope.to.loading = true;
			magezonBuilderService.getBuilderConfig('fonts.' + $scope.font, $scope.onSuccess);
			$scope.listVisible = false;

			$scope.$watch('model.' + $scope.options.key, function(value) {
				$scope.listVisible = false;
			});

			$scope.$watch('font', function(value) {
				$scope.to.loading = true;
				if (value) magezonBuilderService.getBuilderConfig('fonts.' + value, $scope.onSuccess);
			});

			$scope.tagHandler = function (tag) {
			    return null;
			}

			$scope.outsideClick = function() {
				$scope.listVisible = false;
			}

			$scope.removeIcon = function() {
				$scope.model[$scope.options.key] = '';
			}
		},
		link: function(scope, element, attrs, $select) {
            element.find('.mgz-selector-button').click(function(event) {
            	if (scope.listVisible) {
            		setTimeout(function () {
            			$(element).find('.ui-select-toggle').first().trigger('click');
            		}, 300);
            	}
            });        
        }
	}
});