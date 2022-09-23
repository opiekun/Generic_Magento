define([
	'jquery',
	'angular',
    'mage/adminhtml/wysiwyg/tiny_mce/html5-schema',
    'mage/adminhtml/events'
], function ($, angular, html5Schema) {

	if (typeof tinyMceEditors == 'undefined') {
        window.tinyMceEditors = $H({});
    }

	var editor = function($rootScope, $timeout, magezonBuilderFilter) {
		var self     = this;
		self.wysiwyg = angular.copy($rootScope.builderConfig.wysiwyg);
		self.schema  = html5Schema;

		self.initTinymce = function(id, config, onChange, onInit) {
			$timeout(function() {
				if (self.wysiwyg.tinymce4) {
					self.setupTinymce4(id, config, onChange, onInit);
				} else {
					self.setupTinymce3(id, config, onChange, onInit);
				}
			}, 100);
		}

		self.setupTinymce4 = function (id, config, onChange, onInit) {
		    var settings;
		    var deferreds = [];

		    settings = {
		    	selector: '#' + id,
		    	mode: 'inline',
		        theme: 'modern',
		        entity_encoding: 'raw',
		        convert_urls: false,
		        relative_urls: false,
		        remove_script_host: false,
		        verify_html: false,
		        menubar: false,
		        adapter: this,
		        setup: function(editor) {
		        	editor.on('BeforeSetContent', function (evt) {
		        		if (evt.content) {
		        			evt.content = magezonBuilderFilter.encodeContent(evt.content);
		        		}
		        	});

		            editor.on('change', function(evt) {
		            	var value = window.tinyMCE.get(evt.target.id).getContent();
		            	value = magezonBuilderFilter.decodeContent(value);
		            	self._onChange(onChange, value);
		            });
		        }
		    };
		    settings = Object.extend(settings, config);
		    delete settings['plugins'];

		    if (config.baseStaticUrl && config.baseStaticDefaultUrl) {
		        settings['document_base_url'] = config.baseStaticUrl;
		    }
		    // Set the document base URL
		    if (config['document_base_url']) {
		        settings['document_base_url'] = config['document_base_url'];
		    }

		    if (config['files_browser_window_url']) {
		        /**
		         * @param {*} fieldName
		         * @param {*} url
		         * @param {*} objectType
		         * @param {*} w
		         */
		        settings['file_browser_callback'] = function (fieldName, url, objectType, w) {
		            self.openFileBrowser4({
		                win: w,
		                type: objectType,
		                field: fieldName
		            });
		        }.bind(this);
		    }

		    if (config.width) {
		        settings.width = config.width;
		    }

		    if (config.height) {
		        settings.height = config.height;
		    }

		    var plugins = [];
		    //var plugins = config.plugins ? config.plugins.split(' ') : [];
		    var toolbar = config.plugins ? config.toolbar.split(' ') : [];

		    if (config.plugins) {
		        config.plugins.forEach(function (plugin) {
		            var deferred;
		            if (angular.isString(plugin)) {
		                plugins.push(plugin);
		                return;
		            }

		            if (plugins.indexOf(plugin.name) === -1) {
		                plugins.push(plugin.name);
		            }

		            if (toolbar.indexOf(plugin.name) === -1) {
		                toolbar.push('|', plugin.name);
		            }

		            if (!plugin.src) {
		                return;
		            }

		            deferred = $.Deferred();
		            deferreds.push(deferred);

		            require([plugin.src], function (factoryFn) {
		                if (typeof factoryFn === 'function') {
		                    factoryFn(plugin.options);
		                }
		                window.tinyMCE.PluginManager.load(plugin.name, plugin.src);
		                deferred.resolve();
		            });

		            if (deferreds.length) {
		                jQuery.when.apply(jQuery, deferreds).done(function () {
		                    $timeout(function() {
		                    	self._initTinymce(id, onInit, settings);
		                    });
		                });
		            }
		        });
		    }
		    settings['plugins'] = plugins.join(' ');
		    settings['toolbar'] = toolbar.join(' ');
		    if (!deferreds.length) {
		    	self._initTinymce(id, onInit, settings);
		    }
		}

		self.openFileBrowser4 = function (o) {
		    var typeTitle = self.translate('Select Images'),
		        storeId = 0,
		        frameDialog = jQuery('div.mce-container[role="dialog"]');

		    var id   = frameDialog.find('.mce-textbox').eq(0).attr('id');
		    var wUrl = self.wysiwyg.files_browser_window_url +
		            'target_element_id/' + id + '/' +
		            'store/' + storeId + '/';
		    self.mediaBrowserOpener = o.win;
		    self.mediaBrowserTargetElementId = o.field;

		    if (typeof o.type !== 'undefined' && o.type !== '') { //eslint-disable-line eqeqeq
		        wUrl = wUrl + 'type/' + o.type + '/';
		    }

		    frameDialog.hide();
		    jQuery('#mce-modal-block').hide();

		    require(['mage/adminhtml/browser'], function () {
		        MediabrowserUtility.openDialog(wUrl, false, false, typeTitle, {
		            /**
		             * Closed.
		             */
		            closed: function () {
		                frameDialog.show();
		                jQuery('#mce-modal-block').show();
		            }
		        });
		    });
		}

		this.setupTinymce3 = function(id, config, onChange, onInit) {
		    var plugins = 'inlinepopups,safari,pagebreak,style,layer,table,advhr,advimage,emotions,iespell,media,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras';

		    if (config.widget_plugin_src) {
		        //plugins = 'magentowidget,' + plugins;
		    }

		    var magentoPluginsOptions = $H({});
		    var magentoPlugins = '';

		    if (config.plugins) {
		        config.plugins.each(function(plugin) {
		            magentoPlugins = plugin.name + ',' + magentoPlugins;
		            magentoPluginsOptions.set(plugin.name, plugin.options);
		        });
		        if (magentoPlugins) {
		            plugins = '-' + magentoPlugins + plugins;
		        }
		    }

		    var settings = {
		        'entity_encoding': 'raw',
		        mode: 'exact',
		        elements: id,
		        theme: 'advanced',
		        plugins: plugins,
		        theme_advanced_buttons1: magentoPlugins + 'magentowidget,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect',
		        theme_advanced_buttons2: 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,forecolor,backcolor',
		        theme_advanced_buttons3: 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,media,advhr,|,ltr,rtl,|,fullscreen',
		        theme_advanced_buttons4: 'insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak',
		        theme_advanced_toolbar_location: 'top',
		        theme_advanced_toolbar_align: 'left',
		        theme_advanced_statusbar_location: 'bottom',
		        valid_elements: self.schema.validElements.join(','),
		        valid_children: self.schema.validChildren.join(','),
		        theme_advanced_resizing: true,
		        theme_advanced_resize_horizontal: false,
		        convert_urls: false,
		        relative_urls: false,
		        content_css: config.content_css,
		        custom_popup_css: config.popup_css,
		        magentowidget_url: config.widget_window_url,
		        magentoPluginsOptions: magentoPluginsOptions,
		        doctype: '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
		        setup: function (ed) {

		        	var onChange1;

		        	ed.onPreInit.add(self.onEditorPreInit.bind(self));

		        	ed.onInit.add(self.onEditorInit.bind(self));

		            ed.onSubmit.add(function (edi, e) {
		                varienGlobalEvents.fireEvent('tinymceSubmit', e);
		            });

		            ed.onPaste.add(function (edi, e, o) {
		                varienGlobalEvents.fireEvent('tinymcePaste', o);
		            });

		            ed.onBeforeSetContent.add(function (edi, o) {
		                varienGlobalEvents.fireEvent('tinymceBeforeSetContent', o);
		                if (o.content) {
		        			o.content = magezonBuilderFilter.encodeContent(o.content);
		        		}
		            });

		            ed.onSetContent.add(function (edi, o) {
		                varienGlobalEvents.fireEvent('tinymceSetContent', o);
		                self.updateTextArea(onChange, id);
		            });

		            ed.onSaveContent.add(function (edi, o) {
		                varienGlobalEvents.fireEvent('tinymceSaveContent', o);
		                self.updateTextArea(onChange, id);
		            });

		            onChange1 = function (edi, l) {
		                varienGlobalEvents.fireEvent('tinymceChange', l);
		                self.updateTextArea(onChange, id);
		            };

		            ed.onChange.add(onChange1);
                    ed.onKeyUp.add(onChange1);

		            ed.onExecCommand.add(function (edi, cmd) {
		                varienGlobalEvents.fireEvent('tinymceExecCommand', cmd);
		                self.updateTextArea(onChange, id);
		            });
		        }
		    };

		    // Set the document base URL
		    if (config.document_base_url) {
		        settings.document_base_url = config.document_base_url;
		    }

		    if (config.files_browser_window_url) {
		        settings['file_browser_callback'] = function (fieldName, url, objectType, w) {
		            self.openFileBrowser3(id, {
		                win: w,
		                type: objectType,
		                field: fieldName
		            });
		        }.bind(this);
		    }

		    if (config.width) {
		        settings.width = config.width;
		    }

		    if (config.height) {
		        settings.height = config.height;
		    }

		    if (config.settings) {
		        Object.extend(settings, config.settings)
		    }

		    setTimeout(function() {
		        self._initTinymce(id, onInit, settings);
		    }, 1000);

		    return settings;
		}

		self.openFileBrowser3 = function(id, o) {
			var typeTitle,
			storeId = 0,
			frameDialog = jQuery(o.win.frameElement).parents('[role="dialog"]'),
			wUrl = self.wysiwyg.files_browser_window_url +
			'target_element_id/' + id + '/' +
			'store/' + storeId + '/';

			self.mediaBrowserOpener = o.win;
			self.mediaBrowserTargetElementId = o.field;

			if (typeof(o.type) != 'undefined' && o.type != "") {
				typeTitle = 'image' == o.type ? self.translate('Insert Image...') : self.translate('Insert Media...');
				wUrl = wUrl + "type/" + o.type + "/";
			} else {
				typeTitle = self.translate('Insert File...');
			}

			frameDialog.hide();
			jQuery('#mceModalBlocker').hide();

			MediabrowserUtility.openDialog(wUrl, false, false, typeTitle, {
				closed: function() {
					frameDialog.show();
					jQuery('#mceModalBlocker').show();
				}
			});
		}

		self.updateTextArea = function (onChange, id) {
		    var editor = window.tinyMCE.get(id),
		        content;
		    if (!editor) return;
		    self._onChange(onChange, editor.getContent());
		}

		self._initTinymce = function(id, onInit, settings) {
			tinymce.init(settings);
			var editor = tinymce.get(id);
			self._onInit(onInit, editor);
			tinyMceEditors.set(id, self);
		}

		self.getMediaBrowserOpener = function () {
            return this.mediaBrowserOpener;
        }

        self.getMediaBrowserTargetElementId = function () {
            return this.mediaBrowserTargetElementId;
        }

		self._onChange = function(onChange, value) {
			if (angular.isFunction(onChange)) {
				$timeout(function() {
					onChange(value);
				});
			}
		}

		self._onInit = function(onInit, editor) {
			if (angular.isFunction(onInit)) {
				onInit(editor);
			}
		}

		self.onEditorInit = function (editor) {
        }

		self.onEditorPreInit = function (editor) {
            self.applySchema(editor);
        }

		self.applySchema= function (editor) {
            var schema      = editor.schema,
                schemaData  = self.schema,
                makeMap     = window.tinyMCE.makeMap;

            jQuery.extend(true, {
                nonEmpty: schema.getNonEmptyElements(),
                boolAttrs: schema.getBoolAttrs(),
                whiteSpace: schema.getWhiteSpaceElements(),
                shortEnded: schema.getShortEndedElements(),
                selfClosing: schema.getSelfClosingElements(),
                blockElements: schema.getBlockElements()
            }, {
                nonEmpty: makeMap(schemaData.nonEmpty),
                boolAttrs: makeMap(schemaData.boolAttrs),
                whiteSpace: makeMap(schemaData.whiteSpace),
                shortEnded: makeMap(schemaData.shortEnded),
                selfClosing: makeMap(schemaData.selfClosing),
                blockElements: makeMap(schemaData.blockElements)
            });
        }

        /**
         * @param {String} string
         * @return {String}
         */
        self.translate = function (string) {
            return jQuery.mage.__ ? jQuery.mage.__(string) : string;
        }

        self.remove = function(id) {
        	if (window.tinyMCE.get(id)) {
        		window.tinyMCE.get(id).destroy();
        	}
        }
	}

	return editor;

})