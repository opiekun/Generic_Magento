define([
	'jquery',
	'angular',
	'Magezon_Builder/js/factories/FormlyUtils'
], function ($, angular, FormlyUtils) {

	var form = function($rootScope, formlyConfig, magezonBuilderService, $timeout, magezonBuilderConfig) {
		var self = this;

		this.registerTypes = function(array) {
			for (var i = 0; i < array.length; i++) {
				var elem = array[i];
				// DynamicRows
				if (elem.templateOptions.children) {
					self.registerTypes(elem.templateOptions.children, formlyConfig);
				}
				if (elem.hasOwnProperty('fieldGroup')) {
					formlyConfig.setWrapper({
						name: elem.wrapper,
						templateUrl: elem.templateOptions.templateUrl
					});
					self.registerTypes(elem['fieldGroup'], formlyConfig);
				} else {
					var newType = {
						name: elem.type,
						templateUrl: elem.templateOptions.templateUrl
					};
					if (elem.defaultOptions) {
						newType.defaultOptions = Object.assign({}, elem.defaultOptions);
					}
					formlyConfig.setType(newType);
				}
				if (elem.templateOptions.wrapperTemplateUrl) {
					formlyConfig.setWrapper({
						name: elem.data.wrapperType,
						templateUrl: elem.templateOptions.wrapperTemplateUrl
					});
				}
			}
		}

		this.processFormFields = function(fields, callbackFunction) {
			var excludedFields  = ['defaultOptions'];
			var requireElements = [];

			var processRequireElements = function(children) {
				angular.forEach(children, function(elem) {
					if (elem.data && elem.data.element && $.inArray(elem.data.element, requireElements)===-1) {
						requireElements.push(elem.data.element);
					}
					if (elem.fieldGroup) {
						processRequireElements(elem.fieldGroup);
					}
					if (elem.templateOptions.children) {
						processRequireElements(elem.templateOptions.children);
					}
				});
			}
			processRequireElements(fields);

			var extendProperties = ['templateOptions'];
			var mergeElement = function(array, element, data) {
				for (var i = 0; i < array.length; i++) {
					var row = array[i];
					if (row['data'] && row['data']['element'] && row['data']['element'] == element) {
						_.each(extendProperties, function(property) {
							_.each(data[property], function(value, key) {
								if (!row[property].hasOwnProperty(key)) {
									row[property][key] = value;
								}
							});
							delete data[property];
						});
						_.each(data, function(value, key) {
							row[key] = value;
						});
					}
						
					for (var z = 0; z < excludedFields.length; z++) {
						delete row[excludedFields[z]];
					}

					if (row.hasOwnProperty('fieldGroup')) {
						mergeElement(row['fieldGroup'], element, data);
					}
					if (row.hasOwnProperty('fields')) {
						mergeElement(row['fields'], element, data);
					}
					if (row.templateOptions.children) {
						mergeElement(row.templateOptions.children, element, data);
					}
				}
			}

			if (requireElements.length) {
				var loaded = 0;
				_.each(requireElements, function(element, index) {
					require([element], function(data) {
						mergeElement(fields, element, data);
						loaded++;
						if (loaded == requireElements.length) {
	                		callbackFunction(fields);
						}
					});
				});
			} else {
	            callbackFunction(fields);
			}
		}

		this.getForm = function(name, callback, ajaxData) {
			magezonBuilderService.getBuilderConfig(name, function(result) {
				var newFields = angular.copy(result.form);
				var fields = FormlyUtils.processFields(newFields, 'children');
				self.registerTypes(fields);
				self.processFormFields(fields, function(tabs) {
					$timeout(function() {
						$rootScope.$broadcast('afterLoadModalTabs');
						callback(tabs, result);
					});
				});
			});
		}
	}

	return form;

})