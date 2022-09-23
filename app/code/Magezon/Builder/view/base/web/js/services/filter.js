define([
    'jquery',
    'angular'
], function ($, angular) {

    // vendor/magento/module-tinymce-3/view/base/web/tinymce3Adapter.js
    // vendor/magento/magento2-base/lib/web/mage/adminhtml/wysiwyg/tiny_mce/tinymce4Adapter.js

    var filter = function($rootScope, magezonBuilderUrl, magezonBuilderService) {
        var self     = this;
        var wysiwyg  = this.config = angular.copy($rootScope.builderConfig.wysiwyg);
        var tinymce4 = wysiwyg.tinymce4;

        /**
         * Retrieve directives URL with substituted directive value.
         *
         * @param {String} directive
         */
        self.makeDirectiveUrl = function (directive) {
            return this.config['directives_url']
                .replace(/directive/, 'directive/___directive/' + directive)
                .replace(/\/$/, '');
        }

        /**
         * @param {Object} attributes
         * @return {Object}
         */
        self.parseAttributesString = function (attributes) {
            var result = {};
            attributes.gsub(
                /(\w+)(?:\s*=\s*(?:(?:"((?:\\.|[^"])*)")|(?:'((?:\\.|[^'])*)')|([^>\s]+)))?/,
                function (match) {
                    if (magezonBuilderService.isBoolean(match[2])) {
                        result[match[1]] = magezonBuilderService.strToBoolean(match[2]);
                    } else if (magezonBuilderService.isBoolean(match[4])) {
                        result[match[1]] = magezonBuilderService.strToBoolean(match[4]);
                    } else {
                        result[match[1]] = match[2];  
                    }
                }
            );
            return result;
        }

        /**
         * Tinymce has strange behavior with html and this removes one of its side-effects
         * @param {String} content
         * @return {String}
         */
        self.removeDuplicateAncestorWidgetSpanElement = function (content) {
            var parser, doc;

            if (!window.DOMParser) {
                return content;
            }

            parser = new DOMParser();
            doc = parser.parseFromString(content.replace(/&quot;/g, '&amp;quot;'), 'text/html');

            [].forEach.call(doc.querySelectorAll('.magento-widget'), function (widgetEl) {
                var widgetChildEl = widgetEl.querySelector('.magento-widget');

                if (!widgetChildEl) {
                    return;
                }

                [].forEach.call(widgetEl.childNodes, function (el) {
                    widgetEl.parentNode.insertBefore(el, widgetEl);
                });

                widgetEl.parentNode.removeChild(widgetEl);
            });

            return doc.body ? doc.body.innerHTML.replace(/&amp;quot;/g, '&quot;') : content;
        }

        // vendor/magento/module-page-builder/view/adminhtml/web/js/utils/directives.js
        self.convertMediaDirectivesToUrls = function(html) {
            var mediaDirectiveRegExp = /\{\{\s*media\s+url\s*=\s*"?[^"\s\}]+"?\s*\}\}/g;
            var mediaDirectiveMatches = html.match(mediaDirectiveRegExp);
            if (mediaDirectiveMatches) {
                mediaDirectiveMatches.forEach(function (mediaDirective) {
                    var urlRegExp = /\{\{\s*media\s+url\s*=\s*(?:"|&quot;)?(.+)(?=}})\s*\}\}/;
                    var urlMatches = mediaDirective.match(urlRegExp);
                    if (urlMatches && typeof urlMatches[1] !== "undefined") {
                        html = html.replace(mediaDirective, magezonBuilderUrl.getImageUrl(urlMatches[1].replace(/"/g, "").replace(/&quot;/g, "").replace(/'/g, "")));
                    }
                });
            }
            return html;
        }

        self.encodeContent = function(content, convert) {
            if (!content) return;
            //if (convert) {
                content = self.convertMediaDirectivesToUrls(content);
            // } else {
            //  if (tinymce4) {
            //      content = self.tinymce4EncodeDirectives(content);
            //  } else {
            //      content = self.tinymce3EncodeDirectives(content);
            //  }
            // }
            content = self.encodeWidgets(content);
            return content;
        }

        /**
         * Convert {{directive}} style attributes syntax to absolute URLs
         * @param {Object} content
         * @return {*}
         */
        self.tinymce4EncodeDirectives = function (content) {
            // collect all HTML tags with attributes that contain directives
            return content.gsub(/<([a-z0-9\-\_]+[^>]+?)([a-z0-9\-\_]+="[^"]*?\{\{.+?\}\}.*?".*?)>/i, function (match) {
                var attributesString = match[2],
                    decodedDirectiveString;

                // process tag attributes string
                attributesString = attributesString.gsub(/([a-z0-9\-\_]+)="(.*?)(\{\{.+?\}\})(.*?)"/i, function (m) {
                    decodedDirectiveString = encodeURIComponent(Base64.mageEncode(m[3].replace(/&quot;/g, '"') + m[4]));

                    return m[1] + '="' + m[2] + this.makeDirectiveUrl(decodedDirectiveString) + '"';
                }.bind(this));

                return '<' + match[1] + attributesString + '>';
            }.bind(this));
        }

        /**
         * Convert {{directive}} style attributes syntax to absolute URLs
         * @param {Object} content
         * @return {*}
         */
        self.tinymce3EncodeDirectives = function (content) {
            // collect all HTML tags with attributes that contain directives
            return content.gsub(/<([a-z0-9\-\_]+[^>]+?)([a-z0-9\-\_]+="[^"]*?\{\{.+?\}\}.*?".*?)>/i, function (match) {
                var attributesString = match[2],
                    decodedDirectiveString;

                // process tag attributes string
                attributesString = attributesString.gsub(/([a-z0-9\-\_]+)="(.*?)(\{\{.+?\}\})(.*?)"/i, function (m) {
                    decodedDirectiveString = encodeURIComponent(Base64.mageEncode(m[3].replace(/&quot;/g, '"')));

                    return m[1] + '="' + m[2] + this.makeDirectiveUrl(decodedDirectiveString) + m[4] + '"';
                }.bind(this));

                return '<' + match[1] + attributesString + '>';
            }.bind(this));
        }

        self.decodeContent = function(content) {
            if (tinymce4) {
                content = self.tinymce4DecodeDirectives(content);
            } else {
                content = self.tinymce3DecodeDirectives(content);
            }
            content = self.decodeWidgets(content);
            return content;
        }

        /**
         * Convert absolute URLs to {{directive}} style attributes syntax
         * @param {Object} content
         * @return {*}
         */
        self.tinymce4DecodeDirectives = function (content) {
            var directiveUrl = this.makeDirectiveUrl('%directive%').split('?')[0], // remove query string from directive
                // escape special chars in directives url to use in regular expression
                regexEscapedDirectiveUrl = directiveUrl.replace(/([$^.?*!+:=()\[\]{}|\\])/g, '\\$1'),
                regexDirectiveUrl = regexEscapedDirectiveUrl
                    .replace(
                        '%directive%',
                        '([a-zA-Z0-9,_-]+(?:%2[A-Z]|)+\/?)(?:(?!").)*'
                    ) + '/?(\\\\?[^"]*)?', // allow optional query string
                reg = new RegExp(regexDirectiveUrl);

            return content.gsub(reg, function (match) {
                return Base64.mageDecode(decodeURIComponent(match[1]).replace(/\/$/, '')).replace(/"/g, '&quot;');
            });
        }

        /**
         * Convert absolute URLs to {{directive}} style attributes syntax
         * @param {Object} content
         * @return {*}
         */
        self.tinymce3DecodeDirectives = function (content) {
            var directiveUrl = this.makeDirectiveUrl('%directive%').split('?')[0], // remove query string from directive
                // escape special chars in directives url to use in regular expression
                regexEscapedDirectiveUrl = directiveUrl.replace(/([$^.?*!+:=()\[\]{}|\\])/g, '\\$1'),
                regexDirectiveUrl = regexEscapedDirectiveUrl
                    .replace(
                        '%directive%',
                        '([a-zA-Z0-9,_-]+(?:%2[A-Z]|)+\/?)(?:(?!").)*'
                    ) + '/?(\\\\?[^"]*)?', // allow optional query string
                reg = new RegExp(regexDirectiveUrl);

            return content.gsub(reg, function (match) {
                return Base64.mageDecode(decodeURIComponent(match[1]).replace(/\/$/, '')).replace(/"/g, '&quot;');
            });
        }

        /**
         * @param {Object} content
         * @return {*}
         */
        self.encodeWidgets = function(content) {
            if (tinymce4) {
                content = self.tinymce4EncodeWidgets(self.decodeWidgets(content));
                content = self.removeDuplicateAncestorWidgetSpanElement(content);
            } else {
                content = self.tinymce3EncodeWidgets(content);
            }
            return content;
        }

        /**
         * Convert {{widget}} style syntax to image placeholder HTML
         * @param {String} content
         * @return {*}
         */
        self.tinymce4EncodeWidgets = function (content) {
            return content.gsub(/\{\{widget(.*?)\}\}/i, function (match) {
                var attributes = self.parseAttributesString(match[1]),
                    imageSrc,
                    imageHtml = '';

                if (attributes.type) {
                    attributes.type = attributes.type.replace(/\\\\/g, '\\');
                    imageSrc = self.config.placeholders[attributes.type];

                    if (imageSrc) {
                        imageHtml += '<span class="magento-placeholder magento-widget mceNonEditable" ' +
                            'contenteditable="false">';
                    } else {
                        imageSrc = config['error_image_url'];
                        imageHtml += '<span ' +
                            'class="magento-placeholder magento-placeholder-error magento-widget mceNonEditable" ' +
                            'contenteditable="false">';
                    }

                    imageHtml += '<img';
                    imageHtml += ' id="' + Base64.idEncode(match[0]) + '"';
                    imageHtml += ' src="' + imageSrc + '"';
                    imageHtml += ' />';

                    if (self.config.widget_types[attributes.type]) {
                        imageHtml += self.config.widget_types[attributes.type];
                    }

                    imageHtml += '</span>';

                    return imageHtml;
                }
            });
        }

        /**
         * @param {Object} content
         * @return {*}
         */
        self.tinymce3EncodeWidgets = function (content) {
            return content.gsub(/\{\{widget(.*?)\}\}/i, function (match) {
                var attributes = self.parseAttributesString(match[1]),
                    imageSrc,
                    imageHtml;

                if (attributes.type) {
                    attributes.type = attributes.type.replace(/\\\\/g, '\\');
                    imageSrc = this.config['widget_placeholders'][attributes.type];

                    imageHtml = '<img';
                    imageHtml += ' id="' + Base64.idEncode(match[0]) + '"';
                    imageHtml += ' src="' + imageSrc + '"';
                    imageHtml += ' title="' +
                        match[0].replace(/\{\{/g, '{').replace(/\}\}/g, '}').replace(/\"/g, '&quot;') + '"';
                    imageHtml += '>';

                    return imageHtml;
                }
            }.bind(this));
        }

        /**
         * @param {Object} content
         * @return {*}
         */
        self.decodeWidgets = function(content) {
            if (tinymce4) {
                content = self.tinymce4DecodeWidgets(content);
            } else {
                content = self.tinymce3DecodeWidgets(content);
            }
            return content;
        }

        /**
         * Convert image placeholder HTML to {{widget}} style syntax
         * @param {String} content
         * @return {*}
         */
        self.tinymce4DecodeWidgets = function (content) {
            return content.gsub(
                /(<span class="[^"]*magento-widget[^"]*"[^>]*>)?<img([^>]+id="[^>]+)>(([^>]*)<\/span>)?/i,
                function (match) {
                    var attributes = self.parseAttributesString(match[2]),
                        widgetCode;

                    if (attributes.id) {
                        widgetCode = Base64.idDecode(attributes.id);

                        if (widgetCode.indexOf('{{widget') !== -1) {
                            return widgetCode;
                        }
                    }

                    return match[0];
                }
            );
        }

        /**
         * @param {Object} content
         * @return {*}
         */
        self.tinymce3DecodeWidgets = function (content) {
            return content.gsub(/<img([^>]+id=\"[^>]+)>/i, function (match) {
                var attributes = self.parseAttributesString(match[1]),
                    widgetCode;

                if (attributes.id) {
                    widgetCode = Base64.idDecode(attributes.id);

                    if (widgetCode.indexOf('{{widget') !== -1) {
                        return widgetCode;
                    }
                }

                return match[0];
            }.bind(this));
        }

        self.processContent = function(content) {
            var result = content.gsub(/<img\s[^>]*?src\s*=\s*['\"]([^'\"]*?)['\"][^>]*?>/i, function (match) {
                if (match[1].indexOf($rootScope.builderConfig.mediaUrl) !== -1) {
                    return match[0].replace(match[1], self.urlToDirective(match[1]));
                } else {
                    return match[0];
                }
            });
            return result;  
        }

        self.convertImageToDirective = function(content) {
            content = content.replace(/(data-mce-selected="1")/g, '\\$1');
            content = content.gsub(/<img\s[^>]*?src\s*=\s*['\"]([^'\"]*?)['\"][^>]*?>/i, function (match) {
                var result;
                if (match[1].indexOf($rootScope.builderConfig.mediaUrl) !== -1) {
                    var mediaDirective = self.urlToDirective(match[1]);
                    result = match[0].replace(match[1], mediaDirective);
                    result = result.replace('data-mce-src="' + match[1] + '"', '');
                } else {
                    result = match[0];
                }
                return result;
            });
            return content;
        }

        self.urlToDirective = function(imageUrl) {
            var mediaUrl  = self.convertUrlToPathIfOtherUrlIsOnlyAPath($rootScope.builderConfig.mediaUrl, imageUrl);
            var mediaPath = imageUrl.split(mediaUrl);
            return "{{media url=" + mediaPath[1] + "}}";
        }

        self.convertUrlToPathIfOtherUrlIsOnlyAPath = function(url, otherUrl) {
            return self.isPathOnly(otherUrl) ? self.getPathFromUrl(url) : url;
        }

        self.isPathOnly = function(url) {
            return url.indexOf("/") === 0;
        }

        self.getPathFromUrl = function(url) {
            var a = document.createElement("a");
            a.href = url;
            return a.pathname;
        }

    }

    return filter;
});