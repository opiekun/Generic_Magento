define([
    'jquery',
    ], function ($) {
    "use strict";

    return {
        trackPromotion: function(options) {
            if (options.enabled) {
                $(document).ready(function() {

                    var wpPersDl = options.persDataLayer;

                    /**  Track the promotion clicks   */
                    $('[data-track-promo-id]').click(function() {
                        var promoId = $(this).attr('data-track-promo-id'),
                            promoName = $(this).attr('data-track-promo-name'),
                            promoCreative = $(this).attr('data-track-promo-creative'),
                            promoPositionSlot = $(this).attr('data-track-promo-position');

                        var promoObj = {
                            'promotion_id': promoId,
                            'promotion_name': promoName,
                            'creative_name': promoCreative,
                            'creative_slot': promoPositionSlot
                        };

                        window.dataLayer.push({
                            'event': 'promotionClick',
                            'ecommerce': {
                                'promoClick': {
                                    'promotions': [promoObj]
                                }
                            }
                        });

                        wpPersDl.setPromotionClick(promoObj);


                    });
                    /** Track the promotion views */
                    var promotionViews = [];
                    $('[data-track-promo-id]').each(function() {
                        var promoId = $(this).attr('data-track-promo-id'),
                            promoName = $(this).attr('data-track-promo-name'),
                            promoCreative = $(this).attr('data-track-promo-creative'),
                            promoPositionSlot = $(this).attr('data-track-promo-position');

                        promotionViews.push({
                            'promotion_id': promoId,
                            'promotion_name': promoName,
                            'creative_name': promoCreative,
                            'creative_slot': promoPositionSlot
                        });
                    });
                    if (promotionViews.length) {
                        window.dataLayer.push({
                            'event': 'promotionView',
                            'ecommerce': {
                                'promoView': {
                                    'promotions': promotionViews
                                }
                            }
                        });
                    }
                });
            }
        }
    };

});
