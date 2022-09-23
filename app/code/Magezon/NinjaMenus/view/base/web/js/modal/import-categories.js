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
			$timeout,
			form,
			modal,
			profileManager,
			magezonBuilderForm,
			elementManager
		) {
			var parent = $controller('modalBaseController', {$scope: $scope, $uibModalInstance: $uibModalInstance, modal: modal, form: form});
			angular.extend(this, parent);

			var self = this;
			var elements = angular.toJson(profileManager.getJsonElements());
			self.model = {
				shortcode: elements == '[]' ? '' : elements
			};

			$scope.$emit('enableModalSpinner');
			magezonBuilderForm.getForm('modals.importCategories', function(tabs) {
				self.tabs = tabs;
				$scope.$emit('disableModalSpinner');
			});

			self.onSubmit = function() {
				var category = this.findCategory(self.model.category_id);
				if (category) {
					var elements = [];
					if (self.model.import_children) {
						elements = this.prepareElements(category['optgroup']);
					} else {
						var element = this.prepareElement(category);
						element['elements'] = [];
						elements.push(element);
					}
					angular.forEach(elements, function(element) {
						$rootScope.profile.elements.push(element);
					});
					$rootScope.$broadcast('addHistory', {
						type: 'imported_categories',
						title: category.label
					});
					$uibModalInstance.close(category);
				}
			}

	    self.prepareElements = function(categories) {
			var self = this;
	    	var elements = [];
			angular.forEach(categories, function(category) {
				elements.push(self.prepareElement(category));
			});
			return elements;
		}

		self.prepareElement = function(category) {
			var data = angular.merge(elementManager.getNewElement('menu_item'), {
				id: elementManager.getUniqueId(),
				title: category.label,
				type: 'menu_item',
				item_type: 'category',
				category_id: category.id,
				item_align: 'left',
				icon_position: 'left',
				submenu_type: 'mega',
				submenu_position: 'left_edge_parent_item',
				label_position: 'top_right',
				caret: 'fas mgz-fa-angle-down',
				caret_hover: 'fas mgz-fa-angle-up',
				elements: []
			});
			if (category.optgroup) {
				data['elements'] = this.prepareElements(category.optgroup);
			}
			return data;
		}

	    self.findCategory = function (catId, categories) {
        	if (!categories || !categories.length) {
        		var categories = window.ninjamenus.categories;
        	}
        	for (var i = 0; i <= categories.length; i++) {
        		var category = categories[i];
        		if (category) {
	        		if (category['value'] == catId) {
	        			return category;
	        		}
	        		if (category['optgroup'] && category['optgroup'].length) {
	        			var cat = this.findCategory(catId, category['optgroup']);
	        			if (cat) {
	        				return cat;
	        			}
	        		}
	        	}
        	}
        	return false;
        }
		}
	}
});