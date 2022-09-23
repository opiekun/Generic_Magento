define([
	'jquery',
	'angular'
], function ($, angular) {

	var directive = function($document, $rootScope, $timeout) {
		return {
			require: "ngModel",
			link: function(scope, element, attrs, ngModel) {
				element.addClass('mgz-contenteditable-element');
				element.attr('contenteditable', true);

				var type = attrs.type ? attrs.type : 'inline';

				function read() {
					ngModel.$setViewValue(element.html());
				}

				function updatePlaceHolder() {
					if (element.data('placeholder') && !ngModel.$viewValue) {
						element.addClass('placeholder-text');
					} else {
						element.removeClass('placeholder-text');
					}
				}

				ngModel.$render = function() {
					element.html(ngModel.$viewValue || "");
				};

				scope.enableEditing = function() {
					if(scope.element) {
						$rootScope.$broadcast('disableEditing', scope.element);
						$timeout(function () {
							scope.element.builder.editing = true;
						});
					}
				}

				element.bind("click", function(e) {
					var draggableParent = element.closest('.ui-draggable');
					if (draggableParent.length) {
						draggableParent.draggable({ disabled: true });
					}
					scope.enableEditing();
					e.stopPropagation();
				});

				element.bind("focus", function(e) {
					element.trigger('click');
					element.trigger('mouseenter');
				});

				element.bind("blur", function(e) {
					var draggableParent = element.closest('.ui-draggable');
					if (draggableParent.length) {
						draggableParent.draggable({ disabled: false });
					}
				});

				element.bind("blur keyup keypress change", function() {
					updatePlaceHolder();
					scope.$apply(read);
				});

				element.bind("keydown keypress", function (e) {
					if(e.which === 13 && type == 'inline') {
	                    element[0].blur();
	                    e.preventDefault();
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