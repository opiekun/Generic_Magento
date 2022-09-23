define([
    'mageUtils',
    'underscore',
    'uiLayout',
    'Magento_Ui/js/form/form'
], function (utils, _, layout, Form) {
    'use strict';

    return Form.extend({

        getModal: function(type) {

            var modal = _.find(this.elems(), function (elem) {
                return elem.index === type;
            });

            return modal;
        },

        addChild: function (type) {

            var template = {
                parent: '${ $.$data.collection.name }',
                name: '${ $.$data.index }',
                dataScope: '',
                nodeTemplate: '${ $.parent }.' + type
            };

            var child = utils.template(template, {
                collection: this,
                index: type
            });

            layout([child]);

            return this;
        }
    });
});