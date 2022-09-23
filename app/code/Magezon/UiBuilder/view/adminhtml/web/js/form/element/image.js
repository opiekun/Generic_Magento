define([
    'jquery',
    'underscore',
    'mageUtils',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/lib/validation/validator',
    'Magento_Ui/js/form/element/abstract',
    'mage/template',
    'text!Magento_Ui/templates/grid/cells/thumbnail/preview.html',
    'jquery/file-uploader'
], function ($, _, utils, uiAlert, validator, Element, mageTemplate, thumbnailPreviewTemplate) {
    'use strict';

    return Element.extend({
        defaults: {
            template: 'Magezon_UiBuilder/form/field',
            elementTmpl: 'Magezon_UiBuilder/form/element/image',
            showLink: false,
            buttonLabel: '+',
            fileManagerUrl: ''
        },

        /**
         * Calls 'initObservable' of parent
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super();
            this.observe('showLink');
            return this;
        },

        getPreviewUid: function() {
            return this.uid + '-preview';
        },

        openFileManager: function() {
            var fileManagerUrl = this.fileManagerUrl.replace('UID', this.getPreviewUid());
            MgzMediabrowserUtility.openDialog(fileManagerUrl);
        },

        loadImage: function() {
            var img = $('#' + this.getPreviewUid());
            this.value(this.mediaUrl + img.val());
        },

        openPreview: function () {
            var modalHtml = mageTemplate(
                    thumbnailPreviewTemplate,
                    {
                        src: this.value(),
                        alt: '',
                        link: '',
                        linkText: ''
                    }
                ),
                previewPopup = $('<div/>').html(modalHtml);

            previewPopup.modal({
                innerScroll: true,
                modalClass: '_image-box',
                buttons: []
            }).trigger('openModal');
        },

        removeImage: function() {
            this.value('');
            this.showLink(false);
        },

        viewLink: function() {
            this.showLink(!this.showLink());
        }
    });
});
