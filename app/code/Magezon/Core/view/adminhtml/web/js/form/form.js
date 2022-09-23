/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
	'Magento_Ui/js/form/form',
	'Magezon_Core/js/form/adapter'
], function ($, Collection, adapter) {
	return Collection.extend({
        /** @inheritdoc */
        initialize: function () {
            this._super();

            adapter.on({
                'reset': this.reset.bind(this),
                'save': this.save.bind(this, true, {}),
                'saveAndContinue': this.save.bind(this, false, {}),
                'saveAndDuplicate': this.save.bind(this, true, {'back': 'duplicate'}),
                'saveAndNew': this.save.bind(this, true, {'back': 'new'}),
                'saveAndSendEmail': this.save.bind(this, true, {'back': 'save_and_send_email'}),
            }, this.selectorPrefix, this.eventPrefix);

            return this;
        },
	});
})