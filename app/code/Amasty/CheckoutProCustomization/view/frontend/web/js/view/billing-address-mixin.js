define([
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/create-billing-address',
    'uiRegistry'
], function (quote, createBillingAddress, registry) {
    'use strict';

    return function (Component) {
        return Component.extend({
            initialize: function () {
                var self = this;
                this._super();

                if (!window.checkoutConfig.isBillingSameAsShipping
                    && window.checkoutConfig.customerData && window.checkoutConfig.customerData.addresses
                    && window.checkoutConfig.customerData.addresses[
                        window.checkoutConfig.customerData['default_billing']
                    ]
                ) {
                    var defaultBillingAddress = window.checkoutConfig.customerData.addresses[
                        window.checkoutConfig.customerData['default_billing']],
                        billingAddress = createBillingAddress(defaultBillingAddress);

                    registry.get(self.parentName, function () {
                        setTimeout(function () {
                            quote.billingAddress(billingAddress);
                            self.currentBillingAddress(billingAddress);
                            self.isAddressDetailsVisible(true);
                        }, 100)
                    });
                }
            },
        });
    };
});
