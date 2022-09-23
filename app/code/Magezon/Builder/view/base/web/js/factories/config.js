define([
	'jquery',
	'angular'
], function($, angular) {

	var array_diff = function(arr1) {
		var retArr = {}
		var argl = arguments.length
		var k1 = ''
		var i = 1
		var k = ''
		var arr = {}

		  	arr1keys: for (k1 in arr1) {
		  		for (i = 1; i < argl; i++) {
		  			arr = arguments[i]
		  			for (k in arr) {
		  				if (arr[k] === arr1[k1]) {
		          			continue arr1keys;
		      			}
		  			}
		  			retArr[k1] = arr1[k1]
				}
			}
		return retArr
	}

	var array_values = function (input) {
		var tmpArr = []
		var key = ''

		for (key in input) {
			tmpArr[tmpArr.length] = input[key]
		}

		return tmpArr
	}

	return function(formlyConfigProvider) {
		var elements      = {};
		var groups        = {};
		var $config       = {};
		var $directives   = {};

		return {
			registerConfig: function(config) {
				$config = config;
			},

			registerGroup: function(name, group) {
				if (!groups[name]) {
					groups[name] = group;
				} else {
					angular.extend(groups[name], group);
				}
			},

			registerElement: function(name, element) {
				if (!element['element']) {
					if (element['is_collection']) {
						element['element'] = 'Magezon_Builder/js/builder/element/list';
					} else {
						element['element'] = 'Magezon_Builder/js/builder/element/base';
					}
				}
				if (!element['navigator']) element['navigator'] = 'Magezon_Builder/js/navigator/element/base';
				if (element['is_collection']) {
					if (!element['templateUrl']) {
						element['templateUrl'] = 'Magezon_Builder/js/templates/builder/element/list.html'
					}
					if (!element['navigatorTemplateUrl']) {
						element['navigatorTemplateUrl'] = 'Magezon_Builder/js/templates/navigator/element/list.html'
					}
				}

				if (element.hasOwnProperty('visible') && element.visible || !element.hasOwnProperty('visible')) {
					if (!elements[name]) {
						elements[name] = element;
					} else {
						angular.extend(elements[name], element);
					}
				}
			},

			processAllowTypes: function() {
				$allTypes      = [];
				angular.forEach(elements, function(element, index) {
					$allTypes.push(element.type);
				});

				$childrenTypes = [];
	            angular.forEach(elements, function(element, index) {
					$allowedTypes  = element.allowed_types;
					$excludedTypes = element.excluded_types;

	                if (angular.isString($allowedTypes)) {
	                    element.allowed_types = $allowedTypes.split(',');
	                }

	                if (!element.allowed_types && $excludedTypes) {
	                    if (angular.isString($excludedTypes)) {
	                        $excludedTypes = $excludedTypes.split(',');
	                    }
	                    $allowedTypes = array_diff($allTypes, $excludedTypes);
	                    element.allowed_types = array_values($allowedTypes);
	                }

	                if (element.children) {
	                    $childrenTypes.push(element.children);
	                }
	            });

	            $validTypes = array_diff($allTypes, $childrenTypes);
	            angular.forEach(elements, function(element, index) {
	                $allowedTypes  = element.allowed_types;
	                if ($allowedTypes) {
	                    if ($.inArray( element.children, $allowedTypes) === -1) {
							$_allowedTypes        = array_diff($allowedTypes, $childrenTypes);
							element.allowed_types = array_values($_allowedTypes);
	                    }
	                } else {
	                    element.allowed_types = array_values($validTypes);
	                }
	            });
			},

			registerDirectives: function(name, directive) {
				$directives[name] = directive;
			},

			$get: function() {
				return {
					elements: elements,
					groups: groups,
					config: $config,
					directives: $directives
				}
			}
		}
	}
});