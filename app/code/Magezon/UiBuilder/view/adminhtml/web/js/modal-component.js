define([
    'jquery',
    'underscore',
    'Magento_Ui/js/modal/modal-component'
], function ($, _, Modal) {
        'use strict';

        return Modal.extend({
            defaults: {
                initializeDataByDefault: false,
                modalTitle: '',
                modalIcon: '',
                additionalClasses: '',
                currentElement: '',
                fieldsetDataScope: '',
                imports: {
                    data: '${ $.provider }:${ $.dataScope }'
                }
            },

            elementDataCache: [],

            /**
             * Initializes observable properties of instance
             */
            initObservable: function () {
                this._super()
                .observe('initializeDataByDefault modalTitle modalIcon additionalClasses');
                return this;
            },

            /**
             * Open Modal
             */
            openModal: function (element) {
                this.currentElement = element;

                this.beforeOpen();

                this._super();

                this.afterOpen();
            },

            beforeOpen: function () {
                this.source.trigger('data.' + this.fieldsetDataScope + '.beforeOpenModal');

                this.currentElement.beforeOpenModal();

                this.saveElementCache();

                this.loadData();

                this.loadModalData();

                this.initializeDataByDefault(true);
            },

            afterOpen: function () {
                this.activeTab();
                this.currentElement.afterOpenModal();
                this.source.trigger('data.' + this.fieldsetDataScope + '.afterOpenModal');
            },

            loadModalData: function () {
                var element    = this.currentElement;
                var modalTitle = element.getFormElementType().label;
                this.modalTitle(modalTitle);
                this.additionalClasses(element.getFormElementType().additionalClasses);
                this.modalIcon(element.getFormElementType().icon);
            },

            saveElementCache: function () {
                var currentElement = this.currentElement;
                var elemName       = currentElement.elem_name();
                if (!this.elementDataCache.hasOwnProperty(elemName)) {
                    this.elementDataCache[elemName] = [];
                    var fields         = this.editorComponent().fields;
                    for (var i = 0; i < fields.length; i++) {
                        var key = fields[i];
                        this.elementDataCache[elemName][key] = this.currentElement[key]();
                    }
                }
            },

            activeTab: function () {
                _.each(this.elems(), function (elem, index) {
                    if (elem.index === 'tabs') {
                        elem.activeDefaultTab();
                    }
                });
            },

            loadData: function() {
                this.source.trigger('data.' + this.fieldsetDataScope + '.beforeLoadData');
                var fields  = this.editorComponent().fields;
                var element = this.currentElement;
                var data    = {};
                for (var i = 0; i < fields.length; i++) {
                    var tmpVal = element[fields[i]];
                    if (typeof tmpVal === 'function') {
                        tmpVal = element[fields[i]]();
                    }
                    data[fields[i]] = tmpVal;
                }

                var useDefault = data['use_default'];
                for (var i = 0; i < fields.length; i++) {
                    if ($.inArray( fields[i], useDefault ) > -1) {
                        this.source.set(this.fieldsetDataScope + '.use_default.' + fields[i], true);
                    } else {
                        this.source.set(this.fieldsetDataScope + '.use_default.' + fields[i], false);
                    }
                }
                this.source.set(this.fieldsetDataScope, data);
                this.source.trigger('data.' + this.fieldsetDataScope + '.afterLoadData');
            },

            closeModal: function() {
                
                this.currentElement.beforeCloseModal();

                this.source.trigger('data.' + this.fieldsetDataScope + '.beforeCloseModal');

                this._super();

                this.source.trigger('data.' + this.fieldsetDataScope + '.afterCloseModal');

                this.currentElement.afterCloseModal();
                
                this.source.set(this.fieldsetDataScope, []);
            },

            actionDone: function () {
                this.valid = true;
                this.elems().forEach(this.validate, this);
                if (this.valid) {
                    this.saveValues();
                    this.closeModal();
                    this.currentElement.reloadData();
                }
            },

            saveValues: function () {
                this.source.trigger('data.' + this.fieldsetDataScope + '.beforeSaveValues');
                var result      = this.source.get(this.fieldsetDataScope);
                var fields      = this.editorComponent().fields;
                var element     = this.currentElement;
                var use_default = {};
                for (var i = 0; i < fields.length; i++) {
                    var key = fields[i];
                    if ($('input[data-name="' + key + '"]').is(":checked")) {
                        use_default[key] = true;
                        this.currentElement[key](this.elementDataCache[element.elem_name()][key]);
                    } else {
                        this.currentElement[key](result[key]);
                    }
                }
                if (this.currentElement.hasOwnProperty('use_default')) {
                    this.currentElement['use_default'](use_default);
                }
                this.source.trigger('data.' + this.fieldsetDataScope + '.afterSaveValues');
            },

            actionDelete: function() {
                this.editorComponent().deleteItem(this.currentElement);
                this.closeModal();
            }
        })
});