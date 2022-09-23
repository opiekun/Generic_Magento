define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote'
    ],
    function ($, quote) {
        'use strict';

        return {
            validate: function () {
                if (quote.isVirtual()) {
                    return true;
                }

                var isValid = true;
                if (typeof(window.iwdAddressValidationIsValid) !== 'undefined') {
                    isValid = window.iwdAddressValidationIsValid;
                }

                if (!isValid) {
                    if (!$('#shipping_address_id').length && $('#shipping_address_id').val()) {
                        $('#shipping_address_id').trigger('change');
                    } else {
                        $('#co-shipping-form').find('select[name="country_id"]').trigger('change');
                    }
                }

                return isValid;
            }
        };
    }
);
