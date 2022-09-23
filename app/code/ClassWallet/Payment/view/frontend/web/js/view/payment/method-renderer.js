define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'classwallet',
                component: 'ClassWallet_Payment/js/view/payment/method-renderer/classwallet-method'
            }
        );
        return Component.extend({});
    }
);