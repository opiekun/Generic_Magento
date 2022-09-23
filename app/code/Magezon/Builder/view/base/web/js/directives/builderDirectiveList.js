define([
	'jquery',
	'angular'
], function ($, angular) {

	var directive = function(magezonBuilderService, $compile) {
		return {
			scope: {
				group: '@',
				tag: '@',
				htmlTag: '@',
				element: '=?'
			},
			link: function(scope, element, attrs) {
				var parent    = scope.$parent;
				var htmlTag   = scope.htmlTag ? scope.htmlTag : 'div';
				var deferreds = [];
				var items     = {};
				angular.forEach(magezonBuilderService.directives, function(directive, index) {
					var tags = directive.tag ? directive.tag : '';
					var groups  = directive.group ? directive.group : '';
					if (angular.isString(tags)) tags = tags.split(',');
					if (angular.isObject(tags)) tags = Object.values(tags);
					if (angular.isString(groups)) groups = groups.split(',');
					if (angular.isObject(groups)) groups = Object.values(groups);
					if ($.inArray(scope.tag, tags) !== -1 || $.inArray(scope.group, groups) !== -1) {
						if (!directive.hasOwnProperty('disabled') || !directive.disabled) {
							var additionalClasses = directive.additionalClasses ? ' ' + directive.additionalClasses : '';
							additionalClasses += ' magezon-builder-directive-' + directive.type;
							if (directive.group) additionalClasses += ' magezon-builder-directive-group-' + directive.group;
							if (directive['element']) {
								var deferred;
								deferred = $.Deferred();
								deferreds.push(deferred);
								require([directive['element']], function(Directive) {
									var html = '<' + htmlTag + ' class="magezon-builder-directive' + additionalClasses + '" ' + (directive.templateUrl ? 'templateUrl="' + directive.templateUrl + '"' : '') + ' element="element" dynamic-directive element-name="mgz-directive-' + directive.type + '" sort-order="' + (directive['sortOrder'] ? directive['sortOrder'] : 0) + '"></' + htmlTag + '>';
									items[index] = html;
									deferred.resolve();
								});
							} else {
								var html = '<' + htmlTag + ' class="magezon-builder-directive' + additionalClasses + '" ' + (directive.templateUrl ? 'templateUrl="' + directive.templateUrl + '"' : '') + ' element="element" dynamic-directive element-name="mgz-directive-' + directive.type + '" sort-order="' + (directive['sortOrder'] ? directive['sortOrder'] : 0) + '"></' + htmlTag + '>';
								items[index] = html;
							}
						}
					}
				});
				$.when.apply($, deferreds).done(function () {
					if (!Object.keys(items).length) element.remove();
					angular.forEach(items, function(item) {
						if (parent.$parent) {
							var template = angular.element(item);
							var newScope = parent.$parent.$new(true);
							newScope.element = scope.element;
							var html = $compile(template)(newScope);
							element.append(html);
						}
					});
				}.bind(this));
			}
		}
	}
	return directive;
});