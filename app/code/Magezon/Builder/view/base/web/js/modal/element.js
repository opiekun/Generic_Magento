define([
	'jquery',
	'angular'
], function($, angular) {

	return {
		templateUrl: 'Magezon_Builder/js/templates/modal/form.html',
		controller: function(
			$rootScope, 
			$scope, 
			$uibModalInstance, 
			$controller, 
			modal,
			form,
			magezonBuilderForm,
			magezonBuilderFilter
		) {
			var parent = $controller('modalBaseController', {$scope: $scope, $uibModalInstance: $uibModalInstance, modal: modal, form: form});
			angular.extend(this, parent);

			var self     = this;
			var element  = form.element;
			var builder  = form.element.builder;
			$scope.title = element.builder.name;
			self.model   = angular.copy(form.element);
			self.element = form.element;

			$scope.$emit('enableModalSpinner');
			magezonBuilderForm.getForm('elements.' + element.type, function(tabs, result) {
				angular.forEach(tabs[0]['templateOptions']['children'], function(tab, index) {
					if (!tab.fieldGroup) {
						delete tabs[0]['templateOptions']['children'][index];
					}
				});
				tabs[0]['templateOptions']['children'] = tabs[0]['templateOptions']['children'].filter(function(){return true;});
				self.tabs = tabs;
				$scope.activeTab();
				builder.fields = result.fields;
				$scope.spinner = false;
				$scope.$emit('disableModalSpinner');
			});

			$scope.activeTab = function() {
				var activeIndex = 0;
				if (form.activeTab) {
					angular.forEach(self.tabs[0]['templateOptions']['children'], function(tab, index) {
						if (tab.templateOptions.elementId == form.activeTab) {
							activeIndex = index;
							return;
						}
					});
					self.tabs[0]['templateOptions']['activeTab'] = activeIndex;
				} else {
					self.tabs[0]['templateOptions']['activeTab'] = activeIndex;
				}
			}

			self.onSubmit = function() {
				var excludeFields = ['builder', 'elements'];
				var newData = self.model;
				angular.forEach(newData, function(value, key) {
					if ($.inArray(key, excludeFields)===-1) {
						if (angular.isString(value)) {
							value = magezonBuilderFilter.processContent(value);
						}
						element[key] = value;
					}
				});
				$uibModalInstance.close(element);
				$rootScope.$broadcast('addHistory', {
					type: 'edited',
					title: element.builder.name
				});
				$rootScope.$broadcast('editedElement', element);
		    }

			self.findField = function(fieldGroup, value) {
				var result;
				angular.forEach(fieldGroup, function(field, index) {
					if ((field.className.indexOf('mgz_field-' + value + ' ') !== -1)) {
						result = field;
						return;
					}
					if (field.fieldGroup && !result) {
						result = self.findField(field.fieldGroup, value);
					}
					if (field.templateOptions.children && !result) {
						result = self.findField(field.templateOptions.children, value);
					}
				});
				return result;
			}

		    $rootScope.$on('showField', function(event, data) {
				var indexesMap = {};
				var valuesMap = {};

				angular.forEach(data.groupsConfig, function (fields, group) {
	                angular.forEach(fields, function (index) {
	                    if (!indexesMap[index]) {
	                        indexesMap[index] = [];
	                    }
	                    indexesMap[index].push(group);
	                });
	            });

	            angular.forEach(indexesMap, function (groups, field) {
					var visible   = (groups.indexOf(data.value) !== -1);
					var field     = self.findField(self.tabs, field);
					if (field) {
						var hide = !visible;
						field['status'] = !hide;
						if (hide) {
							$('.mgz_field-' + field.key).hide();
						} else {
							$('.mgz_field-' + field.key).show();
						}
						setTimeout(function() {
							if (hide) {
								$('.mgz_field-' + field.key).hide();
							} else {
								$('.mgz_field-' + field.key).show();
							}
						}, 200);
					}
	            });
			});
		}
	}
});