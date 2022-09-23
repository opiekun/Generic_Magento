define([
	'jquery',
	'angular'
], function ($, angular) {

	var profileService = function($rootScope, elementManager, magezonBuilderForm, magezonBuilderService) {

		var self = this;
		var defaultSettings = [];

		magezonBuilderForm.getForm('modals.settings', function(tabs, res) {
			self.defaultSettings = res.defaultValues;
		});

        $rootScope.$on('exportShortcode', function(e) {
			self.export();
			setTimeout(function() {
        		$rootScope.$broadcast('loadStyles');
			});
		});

		$rootScope.$on('importShortcode', function(e) {
			self.import();
			setTimeout(function() {
				$rootScope.$broadcast('loadStyles');
			});
		});

		this.export = function() {
			var content  = this.getTargetContent();
			var _content = this.getContent();
			if (magezonBuilderService.isJSON(_content)) {
				var key = this.getKey();
				if (key) {
					_content = '[' + key + ']' + _content + '[/' + key + ']';
				}
			}
			var result = content.replace(_content, this.getShortCode());
			this.getTargetSelector().val(result);
			this.getTargetSelector().trigger('change');
		}

		this.getShortCode = function() {
			var key = this.getKey();
			var result = '';
			if (key) {
				result += '[' + key + ']';
			}
			result += this.toString();
			if (key) {
				result += '[/' + key + ']';
			}
			return result;
		}

		this.import = function() {
			var content = this.getContent();
			var isJSON  = magezonBuilderService.isJSON(content);
			var profile = this.prepareProfile(content);
			$rootScope.$apply(function() {
				$rootScope.profile = angular.extend($rootScope.profile, profile);
				if (content) {
					if (!isJSON) {
						$rootScope.profile = angular.extend($rootScope.profile, self.defaultSettings);
						$rootScope.$broadcast('addElement', {
							type: 'text',
							openModal: false,
							data: {
								history: false,
								content: content
							}
						});
					}
				}
				$rootScope.$broadcast('exportShortcode');
			});
		}

		this.getTargetSelector = function() {
			return $('#' + this.getTargetId());
		}

		this.getTargetId = function() {
			return $rootScope.builderConfig.targetId;
		}

		this.getKey = function() {
			return $rootScope.builderConfig.profile.key;
		}

		this.getContent = function() {
			var key     = this.getKey();
			var content = this.getTargetSelector().val();
			var regExp  = '\\[' + key + '\\](.*)\\[/' + key + '\\]';
			var matches = content.match(regExp);
			if (matches) content = matches[1];
			return content;
		}

		this.prepareProfile = function(content, force) {
			var key = this.getKey();
			var regExp  = '\\[' + key + '\\](.*)\\[/' + key + '\\]';
			var matches = content.match(regExp);
			if (matches) content = matches[1];
			var profile = {
				builder: elementManager.getBuilderConfig('profile'),
				elements: []
			};
			var isJSON = magezonBuilderService.isJSON(content);
			if (isJSON) {
				var _profile = JSON.parse(content);
				angular.forEach(_profile, function(value, key) {
					if (key !== 'elements') {
						profile[key] = value;
					}
				});
				profile.elements = elementManager.prepareElements(angular.copy(_profile.elements), force);
			}
			if (!profile['pid']) profile['pid'] = magezonBuilderService.getUniqueId();
			return profile;
		}

		this.getJsonElements = function() {
			return this.prepareElements($rootScope.profile.elements);
		}

		this.toString = function() {
			var profile = angular.copy($rootScope.profile);
			var result = {};
			var excludedFields = ['allowed_types', 'builder'];
			angular.forEach(profile, function(value, key) {
				if (excludedFields.indexOf(key) === -1) {
					result[key] = value;
				}
			});
			if (!result['pid']) result['pid'] = magezonBuilderService.getUniqueId();
			result.elements = this.prepareElements($rootScope.profile.elements);
			return angular.toJson(result);
		}

		this.getTargetContent = function() {
            return this.getTargetSelector().val();
		}

		this.prepareElements = function(elements) {
			var _elements = angular.copy(elements);
			var newElements = [];
			angular.forEach(_elements, function(element) {
				var builderElement = elementManager.getElement(element.type);
				if (builderElement.name !== element.builder.name) {
					element.builderName = element.builder.name;
				} else {
					delete element.builderName;
				}
				delete element.builder;
				if (builderElement) {
					if (element.elements) {
						element.elements = self.prepareElements(element.elements);
					}
					newElements.push(element);
				}
			});
			return newElements;
		}

		this.updateElements = function(elements) {
			$rootScope.profile.elements = elements;
		}
	}

	return profileService;
});