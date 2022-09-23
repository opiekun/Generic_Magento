define([
    './abstract',
    "mage/adminhtml/wysiwyg/tiny_mce/setup"
], function (Abstract) {
    'use strict';

    return Abstract.extend({
    	defaults: {
            cols: 15,
            rows: 2,
            elementTmpl: 'ui/form/element/textarea',
    		showButton: false
    	},

        onElementRender: function () {
            this.loadEditor();
        },

        afterLoadData: function () {
            this.loadEditor();
        },

        beforeLoadData: function () {
            this.destroyEditor();
        },

        loadEditor: function () {
            if (window.tinyMceWysiwygSetup) {
                var editor = new tinyMceWysiwygSetup(this.uid, this.editorConfig);
            } else {
                var editor = new wysiwygSetup(this.uid, this.editorConfig);
            }
            editor.setup("exact");
        },

        destroyEditor: function () {
            tinyMCE.execCommand('mceRemoveControl', true, this.uid);
            if (tinymce.get(this.uid)) {
                tinymce.get(this.uid).remove();
            }
        },

    	openEditor: function() {
    		window.mgzUiBuilderWysiwygEditor.open(this.editorUrl, this.uid);
    	}
    });
});