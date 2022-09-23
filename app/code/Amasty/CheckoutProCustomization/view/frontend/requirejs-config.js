var config = {
    'map': {
        '*': {
            'Amasty_CheckoutCore/template/shipping-address/shipping-address.html':
                'Amasty_CheckoutProCustomization/template/shipping-address/shipping-address.html',
            'Magento_Checkout/template/billing-address/details.html':
                'Amasty_CheckoutProCustomization/template/onepage/billing-address/details.html'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/billing-address': {
                'Amasty_CheckoutProCustomization/js/view/billing-address-mixin': true
            }
        }
    }
};

