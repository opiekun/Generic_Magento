/**
 * Copyright Â© Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'ko',
    'Magento_Ui/js/form/element/abstract',
    'mage/translate',
    'Magezon_Core/js/mage/browser'
], function ($, ko, Element, $t) {
    'use strict';

    return Element.extend({
        defaults: {
            chooseBtnLabel: $t('Select Image'),
            tmp: null,
            showPreview: false,
            fileUrl: '',
            fileName: '',
            fileSize: '',
            previewHeight: '',
            previewWidth: '',

            tracks: {
                showPreview: true,
                fileUrl: true,
                fileName: true,
                fileSize: true,
                previewHeight: true,
                previewWidth: true,
            }
        },

        /**
         * Initializes UISelect component.
         *
         * @returns {UISelect} Chainable.
         */
        initialize: function () {
            this._super();

            if (this.tmp && this.tmp.name) {
                this.fileSize    = this.formatSize(this.tmp.size);
                this.fileName    = this.processFileName(this.tmp.name);
                this.fileUrl     = this.tmp.url;
                this.showPreview = true;
            }

            return this;
        },

        /**
         * Removes provided file from thes files list.
         *
         * @param {Object} file
         * @returns {FileUploader} Chainable.
         */
        chooseImage: function () {
            if (!this.disabled()) {
                window.MgzMediabrowserUtility.openDialog(window.mgzFilesBrowserWindowUrl + 'target_element_id/' + this.uid + '/', false, false, 'Insert Image', {closed: function() { jQuery('#mceModalBlocker').show()}})
            }
            return this;
        },

        /**
         * Formats incoming bytes value to a readable format.
         *
         * @param {Number} bytes
         * @returns {String}
         */
        formatSize: function (bytes) {

            var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'],
                i;

            if (bytes === 0) {
                return '0 Byte';
            }

            i = window.parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));

            return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
        },

        /**
         * Removes provided file from thes files list.
         *
         * @param {Object} file
         * @returns {FileUploader} Chainable.
         */
        removeFile: function (file) {
            if (!this.disabled()) {
                this.showPreview = false;
                this.value('');
            }
            return this;
        },

        /**
         * Handler of the preview image load event.
         *
         * @param {Object} file - File associated with an image.
         * @param {Event} e
         */
        onPreviewLoad: function (element, event) {
            var img            = event.currentTarget;
            this.previewWidth  = img.naturalWidth;
            this.previewHeight = img.naturalHeight;
        },

        /**
         * Update the preview, submit ajax get file size
         */
        reloadImage: function(element, event) {
            var self  = this;
            var input = event.currentTarget;
            var file  = $(input).val();
            if (file) {
                this.showPreview = true;
                this.fileUrl     = window.mgzMediaUrl + file;
                this.fileName    = this.processFileName(file);
                $.ajax({
                    type: 'HEAD',
                    url: this.fileUrl,
                    complete: function(xhr) {
                        var size = self.formatSize(xhr.getResponseHeader('Content-Length'));
                        if (size) {
                            self.fileSize = size;
                        } else {
                            self.fileSize = '';
                        }
                    }
                });
            }
        },

        /**
         * Convert real image, remove wysiwyg
         */
        processFileName: function(name) {
            var nameE = name.split("/");
            return nameE[nameE.length-1];
        }
    });
});