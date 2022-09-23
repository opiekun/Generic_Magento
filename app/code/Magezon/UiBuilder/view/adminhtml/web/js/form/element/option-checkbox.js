define([
    './single-checkbox',
    'uiRegistry'
], function (Checkbox, registry) {
    'use strict';

    return Checkbox.extend({
        defaults: {
            clearing: false,
            parentSelections: '',
            loaded: false
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super().
                observe('elementTmpl');

            return this;
        },

        /**
         * @inheritdoc
         */
        onUpdate: function () {
            if (this.prefer === 'radio' && this.checked() && !this.clearing) {
                this.clearValues();
            }

            this._super();
        },

        /**
         * Clears values in components like this.
         */
        clearValues: function () {
            this.loaded = false;

            var records = registry.get(this.retrieveParentName(this.parentSelections)),
                index = this.index,
                uid = this.uid;

            records.elems.each(function (record) {
                record.elems.filter(function (comp) {
                    return comp.index === index && comp.uid !== uid;
                }).each(function (comp) {
                    comp.clearing = true;
                    comp.clear();
                    comp.clearing = false;
                });
            });
        },

        /**
         * Retrieve name for the most global parent with provided index.
         *
         * @param {String} parent - parent name.
         * @returns {String}
         */
        retrieveParentName: function (parent) {
            return this.name.replace(new RegExp('^(.+?\\.)?' + parent + '\\..+'), '$1' + parent);
        }
    });
});
