define([
	'jquery',
	'angular'
], function ($, angular) {

	var magezonBuilderService = function($rootScope, magezonBuilderUrl, elementManager) {
		var self = this;
		self.data = {};
		self.cacheRequests = {};

		this.viewMode = 'xl';

		this.setViewMode = function(viewMode) {
			this.viewMode = viewMode;
		}

		this.getViewMode = function(viewMode) {
			return this.viewMode;
		}

		this.getBuilderConfig = function(requestKey, callbackFunction) {
			var self = this;
			if (!self.data.hasOwnProperty(requestKey)) {
				if (!self.cacheRequests.hasOwnProperty(requestKey)) {
					$.ajax({
		                url: magezonBuilderUrl.getUrl('mgzbuilder/ajax/loadConfig'),
		                type:'POST',
		                data: {
		                	area: $rootScope.builderConfig.area,
		                	handle: $rootScope.builderConfig.handle,
		                	key: requestKey,
		                	class: $rootScope.builderConfig.profile.builder
		                },
		            	dataType: 'json',
		                success: function(data) {
	                		self.data[requestKey] = data.config;
	                		self.processBuilderConfig(requestKey, data.config);
		                }
		            });
				}
				if (!self.cacheRequests.hasOwnProperty(requestKey)) self.cacheRequests[requestKey] = [];
				self.cacheRequests[requestKey].push(callbackFunction);
			} else if (typeof callbackFunction === 'function') {
				callbackFunction(self.data[requestKey]);
			}
		}

		this.processBuilderConfig = function(requestKey, result) {
		    angular.forEach(self.cacheRequests[requestKey], function(callbackFunction) {
		    	if (angular.isFunction(callbackFunction)) {
		    		callbackFunction(result);
		    	} else {
		    		callbackFunction = result;
		    	}
		    });
		}

		this.saveToCache = function(key, data) {
			this.data[key] = data;
		}

		this.getFromCache = function(key) {
			if (this.data.hasOwnProperty(key)) {
				return this.data[key];
			}
			return false;
		}

		this.getData = function() {
			return this.data;
		}

	    /**
	     * Processing options list
	     *
	     * @param {Array} array - Property array
	     * @param {String} separator - Level separator
	     * @param {Array} created - list to add new options
	     *
	     * @return {Array} Plain options list
	     */
	    this.flattenCollection = function(array, separator, created) {
	        var i = 0,
	            length,
	            childCollection;

	        array = _.compact(array);
	        length = array.length;
	        created = created || [];

	        for (i; i < length; i++) {
	            created.push(array[i]);

	            if (array[i].hasOwnProperty(separator)) {
	                childCollection = array[i][separator];
	                delete array[i][separator];
	                this.flattenCollection.call(this, childCollection, separator, created);
	            }
	        }

	        return created;
	    }

	    /**
	     * Set levels to options list
	     *
	     * @param {Array} array - Property array
	     * @param {String} separator - Level separator
	     * @param {Number} level - Starting level
	     * @param {String} path - path to root
	     *
	     * @returns {Array} Array with levels
	     */
	    this.setProperty = function(array, separator, level, path) {
	        var i = 0,
	            length,
	            nextLevel,
	            nextPath;

	        array = _.compact(array);
	        length = array.length;
	        level = level || 0;
	        path = path || '';

	        for (i; i < length; i++) {
	            if (array[i]) {
	                _.extend(array[i], {
	                    level: level,
	                    path: path
	                });
	            }

	            if (array[i].hasOwnProperty(separator)) {
	                nextLevel = level + 1;
	                nextPath = path ? path + '.' + array[i].label : array[i].label;
	                this.setProperty.call(this, array[i][separator], separator, nextLevel, nextPath);
	            }
	        }

	        return array;
	    }

	    /**
	     * Preprocessing options list
	     *
	     * @param {Array} nodes - Options list
	     *
	     * @return {Object} Object with property - options(options list)
	     *      and cache options with plain and tree list
	     */
	    this.parseOptions = function(nodes) {
	        var caption,
	            value,
	            cacheNodes,
	            copyNodes;

	        nodes = this.setProperty(nodes, 'elements');
	        copyNodes = JSON.parse(JSON.stringify(nodes));
	        cacheNodes = this.flattenCollection(copyNodes, 'elements');

	        nodes = _.map(nodes, function (node) {
	            value = node.value;

	            if (value == null || value === '') {
	                if (_.isUndefined(caption)) {
	                    caption = node.label;
	                }
	            } else {
	                return node;
	            }
	        });

	        return {
	            options: _.compact(nodes),
	            cacheOptions: {
	                plain: _.compact(cacheNodes),
	                tree: _.compact(nodes)
	            }
	        };
	    }

	    this.uniqueid = function (size) {
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

		this.isBackend = function () {
			return $('#html-body').length ? true : false;
		}

		this.isJSON = function (str) {
			try {
				var result = JSON.parse(str);
				if (angular.isNumber(result)) {
					return false;
				}
			} catch (e) {
				return false;
			}
			return true;
		}

		this.getDesignValue = function(elem, key) {
			var viewMode = this.getViewMode();
			var result = elem[key];
			var start = false;
			var sizes = ['lg', 'md', 'sm', 'xs'];
			if (elem.device_type == 'all') return result;
			angular.forEach(sizes, function(v, k) {
				if (v == viewMode) {
					start = true;
				}
				if (start &&  elem[v + '_' + key]) {
					result = elem[v + '_' + key];
					start = false;
				}
			});
			return result;
		}

		this.setDesignValue = function(elem, key, value) {
			var viewMode = this.getViewMode();
			var start    = false;
			var sizes    = ['lg', 'md', 'sm', 'xs'];
			var _key;
			if (elem.device_type == 'all') {
				elem[_key] = value;
				return;
			}
			angular.forEach(sizes, function(v, k) {
				if (v == viewMode) {
					start = true;
				}
				if (start &&  elem[v + '_' + key]) {
					_key = v + '_' + key;
					start = false;
				}
			});
			if (!_key) _key = 'md';
			elem[_key] = value;
		}

		this.getResponiveValue = function(elem, key) {
			var viewMode = this.getViewMode();
			var result = elem[key];
			var start = false;
			var sizes = ['xl', 'lg', 'md', 'sm', 'xs'];
			angular.forEach(sizes, function(v, k) {
				if (v == viewMode) {
					start = true;
				}
				if (start &&  elem[v + '_' + key]) {
					result = elem[v + '_' + key];
					start = false;
				}
			});
			return result;
		}

		this.setResponiveValue = function(elem, key, value) {
			var viewMode = this.getViewMode();
			var start    = false;
			var sizes    = ['xl', 'lg', 'md', 'sm', 'xs'];
			var _key;
			angular.forEach(sizes, function(v, k) {
				if (v == viewMode) {
					start = true;
				}
				if (start &&  elem[v + '_' + key]) {
					_key = v + '_' + key;
					start = false;
				}
			});
			if (!_key) _key = 'md_' + key;
			elem[_key] = value;
		}

		this.getFromLocalStorage = function(key) {
			if (window.localStorage) {
				key = 'magezon.' + key;
				var result = window.localStorage[key];
				if (this.isJSON(result)) {
					result = angular.fromJson(result);
				}
            	return result;
        	}
        }

		this.saveToLocalStorage = function(key, value) {
			if (window.localStorage) {
				key = 'magezon.' + key;
				if (angular.isArray(value) || angular.isObject(value)) {
					value = angular.toJson(value);
				}
            	return window.localStorage[key] = value;
        	}
        }

        this.elemPost = function(elem, url, data, isJson, successFunction, errorFunction, finallyFunction) {
        	var spinnerHtml = '<div class="mgz-spinner"><i></i></div>';

        	var el = elementManager.getEl(elem);

        	if (!el.children('.mgz-spinner').length) {
        		el.prepend(spinnerHtml);
        	}

        	if (!data) data = {};
        	if (angular.isObject(data)) {
        		data['form_key'] = window.FORM_KEY;
        	}
        	$.ajax({
                url: magezonBuilderUrl.getUrl(url),
                type: 'POST',
                data: data,
            	dataType: isJson ? 'json' : 'text',
                success: function(res) {
                	if (typeof successFunction === 'function') {
            			successFunction(res);
            		}
            		if (typeof finallyFunction === 'function') {
            			finallyFunction(res);
            		}
            		el.children('.mgz-spinner').remove();
                },
                error: function(jq, status, message) {
                	console.log(jq);
                	console.log(status);
                	console.log(message);
                	if (typeof errorFunction === 'function') {
                		errorFunction(jq, status, message);
                	}
                	if (typeof finallyFunction === 'function') {
            			finallyFunction(jq, status, message);
            		}
            		el.children('.mgz-spinner').remove();
                }
            });
        }

        this.post = function(url, data, isJson, successFunction, errorFunction, finallyFunction) {
        	if (!data) data = {};
        	if (angular.isObject(data)) {
        		data['form_key'] = window.FORM_KEY;
        	}
        	$.ajax({
                url: magezonBuilderUrl.getUrl(url),
                type: 'POST',
                data: data,
            	dataType: isJson ? 'json' : 'text',
                success: function(res) {
                	if (typeof successFunction === 'function') {
            			successFunction(res);
            		}
            		if (typeof finallyFunction === 'function') {
            			finallyFunction(res);
            		}
                },
                error: function(jq, status, message) {
                	console.log(jq);
                	console.log(status);
                	console.log(message);
                	if (typeof errorFunction === 'function') {
                		errorFunction(jq, status, message);
                	}
                	if (typeof finallyFunction === 'function') {
            			finallyFunction(jq, status, message);
            		}
                }
            });
        }

        this.capitalize = function(str) {
        	return str[0].toUpperCase() + str.slice(1);
        }

        //https://github.com/kvz/locutus/blob/master/src/php/strings/stripslashes.js
		this.removeslashes = function(str) {
			return (str + '')
			.replace(/\\(.?)/g, function (s, n1) {
				switch (n1) {
					case '\\':
					return '\\'
					case '0':
					return '\u0000'
					case '':
					return ''
					default:
					return n1
				}
			});
        }

        //https://github.com/kvz/locutus/blob/master/src/php/strings/addcslashes.js
        this.addslashes = function(str) {
            return (str + '')
            .replace(/[\\"']/g, '\\$&')
            .replace(/\u0000/g, '\\0')
            .replace(/{/g, '\\{')
            .replace(/}/g, '\\}');
        }

        this.isBoolean = function(str) {
            if (str) {
                var _str = str.toLowerCase();
                if (_str == 'true' || _str == 'false' || _str == '1' || _str == '0') {
                    return true;
                }
            }
            return false;
        }

        this.strToBoolean = function(str) {
            if (this.isBoolean(str)) {
                var _str = str.toLowerCase();
                if (_str == 'true' || _str == '1') return true;
                if (_str == 'false' || _str == '0') return false;
            }
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

	    this.isEmpty = function(obj){
	    	return Object.keys(obj).length == 0;
	    }
	}

	return magezonBuilderService;
});