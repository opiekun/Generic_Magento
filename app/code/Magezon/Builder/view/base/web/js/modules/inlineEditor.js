define([
	'jquery',
	'angular'
], function ($, angular) {

	var directive = function($rootScope, magezonBuilderEditor, magezonBuilderService, magezonBuilderFilter, $document, $timeout) {
		return {
			require: "ngModel",
			link: function(scope, element, attrs, ngModel) {
				element.addClass('mgz-inline-editor');
				element.attr('contenteditable', true);

				scope.id = magezonBuilderService.uniqueid();
				scope.wysiwyg = Object.extend(angular.copy($rootScope.builderConfig.wysiwyg), scope.wysiwyg);
				scope.wysiwyg['inline'] = true;
				scope.wysiwyg['fixed_toolbar_container'] = '.' + scope.element.id + ' .mgz-element-inner';
				element.attr('id', scope.id);

				ngModel.$render = function() {
					element.html(magezonBuilderFilter.encodeContent(ngModel.$viewValue) || "");
				};

				element.bind("click", function(e) {
					$rootScope.$broadcast('disableEditing', scope.element);
					$timeout(function () {
						scope.element.builder.editing = true;
					}, 1000);
					e.stopPropagation();
				});

				if ($rootScope.builderConfig.wysiwyg.tinymce4) {
					element.on('mouseenter', function() {
						magezonBuilderEditor.initTinymce(scope.id, scope.wysiwyg, function(value) {
							ngModel.$setViewValue(magezonBuilderFilter.decodeContent(value));
						});
					});
				}

				element.bind("blur", function(e) {
					if (scope.element) {
						$timeout(function () {
							scope.element.builder.editing = false;
						});
					}
				});

				scope.$on('disableEditing', function(e, elem) {
					if (scope.element) {
						if (elem.id !== scope.element.id) {
							scope.element.builder.editing = false;
						}
					}
				});
			}
		}
	}

	return directive;
});