/*browser:true*/
/*global define*/
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
                type: 'chargelogic_connect',
                component: 'ChargeLogic_Connect/js/view/payment/method-renderer/connect-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);