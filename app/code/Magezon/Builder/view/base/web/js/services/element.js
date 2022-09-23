define([
	'jquery',
	'angular'
], function ($, angular) {

	var elementManagerService = function($rootScope) {
		var self = this;
		this.elements = {};
		this.groups   = {};

		this.registerElements = function(elements) {
			var newElements = {};
			angular.forEach(elements, function(element, index) {
				if (!element.hasOwnProperty('disabled') || !element['disabled']) {
					newElements[index] = element;
				}
			});
			this.elements = newElements;
		}

		this.getElement = function(type) {
			if (angular.isObject(type)) {
				type = type.type;
			}
			return this.elements[type];
		}

		this.prepareElements = function(elements, force) {
			var newElements = [];
			angular.forEach(elements, function(_element) {
				if (self.getElement(_element.type)) {
					newElements.push(self.prepareElement(_element, force));
				}
			});
			return newElements;
		}

		this.prepareElement = function(element, force) {
			var oldId;
			element['builder'] = self.getBuilderConfig(element.type);

			if (element['id'] && force) {
				oldId = element['id'];
			}
			if ($rootScope.builderConfig.area == 'bfb') {
				if (!element['id'] || force) element['id'] = self.getUniqueId(7);
			} else {
				element['id'] = self.getUniqueId(7);
			}

			// if (element['id'] && force) {
			// 	oldId = element['id'];
			// }
			// if (!element['id'] || force) element['id'] = self.getUniqueId(7);
			
			if (element.builderName && element.builderName!=element['builder'].name) {
				element['builder'].name = element.builderName;
			}
			if (element.hasOwnProperty('elements')) {
				element['elements'] = self.prepareElements(element.elements, force);
			}
			if (force && oldId) {
				var _elem = $('#' + oldId + '-style');
				if (_elem.length) {
					var oldId = '\\.' + oldId;
					var newId = '.' + element.id;
					var regex = new RegExp(oldId, 'g');
					var styleHtml = _elem[0].outerHTML.replace(regex, newId);
					styleHtml = styleHtml.replace(oldId + '-style', element.id + '-style');
					_elem.after(styleHtml);
				}
			}
			return element;
		}

		this.getNewElement = function(type) {
			var builderElement = this.getElement(type);
			var element = angular.copy(builderElement.defaultValues);
			element['type'] = type;
			this.prepareElement(element, true);
			if (builderElement.is_collection) {
				element['elements'] = [];
			}
			return element;
		}

		this.getBuilderConfig = function(type) {
			if (type=='profile') {
				return {
					is_collection: true
				}
			}
			var config = angular.copy(this.getElement(type));
			config = angular.merge(config, {
				active: false,
				visible: true,
				control: true,
				actived: false,
				additionalClasses: [],
				navigator: {}
			});
			return config;
	    }

	    this.updateElementConfig = function(type) {
	    	if (!self.elements[type]['loadConfig']) {
		    	var requestKey = 'elements.' + type;
		    	$.ajax({
	                url: this.getUrl('mgzbuilder/ajax/loadConfig'),
	                type:'POST',
	                data: {
	                	key: requestKey,
	                	class: $rootScope.builderConfig.profile.builder
	                },
	            	dataType: 'json',
	                success: function(data) {
	            		self.elements[type]['fields'] = data.config.fields;
	            		self.elements[type]['loadConfig'] = true;
	                }
	            });
		    }
	    }

		this.getUrl = function(url) {
			return $rootScope.builderConfig.baseUrl + url;
		}

		this.getUniqueId = function (size) {
	        var code = Math.random() * 25 + 65 | 0,
	            idstr = String.fromCharCode(code);

	        size = size || 12;

	        while (idstr.length < size) {
	            code = Math.floor(Math.random() * 42 + 48);

	            if (code < 58 || code > 64) {
	                idstr += String.fromCharCode(code);
	            }
	        }

	        return idstr.toLowerCase();
	    }

	    this.canReplace = function(elem) {
			if (!elem.builder.modalVisible) {
				return false;
			}
			return true;
		}

		this.isVisibleElement = function(elem) {
			if (elem.hasOwnProperty('modalVisible') && !elem['modalVisible']) return false;
			return true;
		}

		this.getElementType = function(eleType) {
			var _types = eleType.split('-');
			var type = '';
			for (var i = 0; i < _types.length; i++) {
				var _types1 = _types[i].split('_');
				for (var x = 0; x < _types1.length; x++) {
					type += (_types1[x].charAt(0).toUpperCase() + _types1[x].substr(1));
				}
			}
			return type;
		}

		this.getEl = function(elem) {
			return $('.mgz-builder .' + elem.id);
		}

		// Clone parent styles
		this.previewStyles = function(elem) {
			var _elem = $('#' + elem.builder.oldId + '-style');
			var oldId = '\\.' + elem.builder.oldId;
			var newId = '.' + elem.id;
			var regex = new RegExp(oldId, 'g');
			var styleHtml = _elem[0].outerHTML.replace(regex, newId);
			styleHtml = styleHtml.replace(elem.builder.oldId + '-style', elem.id + '-style');
			$('#' + elem.builder.oldId + '-style').after(styleHtml);
			if (elem.elements) {
				angular.forEach(elem.elements, function(_elem, index) {
					self.previewStyles(_elem);
				});
			}
		}
	}

	return elementManagerService;
});