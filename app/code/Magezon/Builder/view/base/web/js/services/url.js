define([
	'jquery',
	'angular',
], function ($, angular) {

	var magezonBuilderUrl = function($rootScope, elementManager) {

	    this.getTemplateUrl = function(elem, defaultTemplate) {
	    	var templateUrl;
	    	var matches = elem.context.localName.match("mgz-element-navigator-(.*)-mgz");
	    	if (matches) {
	    		var name = matches[1];
				templateUrl = elementManager.getElement(name).navigatorTemplateUrl;
	    	} else {
				var matches2 = elem.context.localName.match("mgz-element-(.*)-mgz");
				if (matches2) {
					var name = matches2[1];
					templateUrl = elementManager.getElement(name).templateUrl;
				} else {
					templateUrl = elem.attr('templateUrl');
					if (!templateUrl) templateUrl = elem.attr('template-url');
					if (!templateUrl) templateUrl = elem.parent().attr('template-url');
					if (!templateUrl) templateUrl = elem.parent().attr('templateUrl');
				}
	    	}
			if (!templateUrl && defaultTemplate) templateUrl = defaultTemplate;
	    	return this.getViewFileUrl(templateUrl);
	    }

		this.getViewFileUrl = function(file) {
			if (file.indexOf('http') === -1) {
				return $rootScope.builderConfig.viewFileUrl + file;
			} else {
				return file;
			}
		}

		this.getImageUrl = function(file) {
			if (file && (file.indexOf('http:://') === -1 || file.indexOf('https://') === -1)) {
				return $rootScope.builderConfig.mediaUrl + file;
			} else {
				return file;
			}
		}

		this.getUrl = function(url, params) {
			if (url.indexOf('http') === -1) {
				url = $rootScope.builderConfig.baseUrl + url;
			}
			if (params) {
				url += '?' + $.param(params);
			}
			return url;
		}

		this.getFrontendUrl = function(url, params) {
			if (url.indexOf('http') === -1) {
				url = $rootScope.builderConfig.frontend_url + url;
			}
			if (params) {
				url += '?' + $.param(params);
			}
			return url;
		}
	}

	return magezonBuilderUrl;
});