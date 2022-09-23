define(
    [
        'underscore',
        'jquery',
        'uiComponent',
        'Magento_Catalog/js/storage-manager'
    ],
    function (_, $, Component, storageManager) {
        'use strict';

        return function(optionConfig){
            var recentlyViewedProductIds = [],
            currentTime = new Date().getTime() / 1000,
            recentlyViewedProductData = {},
            recentlyViewedProducts = storageManager().recently_viewed_product;
            if (recentlyViewedProducts) {
                recentlyViewedProductData = recentlyViewedProducts.get();
            }

        _.each(recentlyViewedProductData, function (id) {
            if (
                currentTime - id['added_at'] < ~~recentlyViewedProducts.lifetime
            ) {
                recentlyViewedProductIds.push(id['product_id'])

            }
        }, this);
            $.ajax({
                url: optionConfig.ajaxUrl,
                method: 'POST',
                cache: false,
                data: {
                    is_ajax: 1,
                    request_type: optionConfig.requestType,
                    product_ids: recentlyViewedProductIds
                },
                success: function (result) {
                    if(result.errors) {
                        //$('#' + optionConfig.requestType).remove();
                    }
                    $('#' + optionConfig.requestType).html(result.block);

                }
            });
        };
    });
