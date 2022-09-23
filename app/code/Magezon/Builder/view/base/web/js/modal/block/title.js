define([
	'jquery',
	'angular'
], function($, angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/modal/block/title.html');
			},
			controller: function($rootScope, $scope) {
				var loaded = false;
				$scope.title    = $scope.$parent.title;
				$scope.subtitle = $scope.$parent.subtitle;
				$scope.$watch('title', function(title) {
					if (loaded) {
						$scope.$parent.mgz.element.builder.name = title;
						$rootScope.$broadcast('exportShortcode');
					}
					loaded = true;
				});
			},
			link: function(scope, element) {
				if (scope.$parent.mgz.element) {
					element.find('.mgz-icon-edit').click(function(event) {
						var span = element.find('.mgz-modal-title-inner');
						var t = document.createRange();
						var i = window.getSelection();
						t.selectNodeContents(span[0]);
						i.removeAllRanges();
						i.addRange(t);
					});
				} else {
					element.find('span').eq(0).removeAttr('contenteditable').removeClass('mgz-contenteditable-element');
				}
			}
		}
	}

	return directive;
});