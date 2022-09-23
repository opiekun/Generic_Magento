var OWL = {
    init: function () {
    },

    load: function () {
        this.arrows();
    },

    resize: function () {
        this.arrows();
    },

    ajaxComplete: function () {
        this.loader();
    },

    arrows: function () {
        var carouselElement = jQuery('[class*="owl-carousel-products-"]'),
            windowWidth = jQuery(window).width(),
            carouselWidth = carouselElement.width(),
            carouselContainer = carouselWidth + 120,
            carouselControls = carouselElement.find('.owl-nav');

        var rowParent = jQuery('.owl-prev').parents().find('.row').get(0);
        var leftPosition = 0,
            rightPosition = 0;
        if (rowParent) {
            leftPosition = Math.abs(parseInt(jQuery(rowParent).css('margin-left')));
            rightPosition = Math.abs(parseInt(jQuery(rowParent).css('margin-right')));
        }

        if (carouselContainer >= windowWidth) {
            carouselControls.addClass('fullscreen').find('.owl-prev').css({
                'left': leftPosition
                // 'top': 0
            });
            carouselControls.addClass('fullscreen').find('.owl-next').css({
                'right': rightPosition
                // 'top': 0
            });
        } else {
            carouselControls.find('.owl-prev').removeClass('fullscreen').removeAttr('style');
            carouselControls.find('.owl-next').removeClass('fullscreen').removeAttr('style');
        }
    },

    loader: function () {
        jQuery('.custom-slider #pre-div, .products.products-grid #pre-div').each(function(){
            jQuery(this).fadeOut('slow');
        });
    },
    msieversion: function () {
        var ua = window.navigator.userAgent,
            msie = ua.indexOf("MSIE ");
        if(msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./) || /Edge/.test(navigator.userAgent)){
            setTimeout(function(){
                if(jQuery('body').hasClass('theme-pearl')){
                    jQuery('.owl-prev').addClass('icon-angle-left');
                    jQuery('.owl-next').addClass('icon-angle-right');
                }else{
                    jQuery('.owl-prev').addClass('icon-angle-left-magento');
                    jQuery('.owl-next').addClass('icon-angle-right-magento');
                }
            }, 1000);
        }
    }
};

require(['jquery'],
    function ($) {
        $(document).ready(function () {
            OWL.init();
            OWL.msieversion();
        });

        $(window).on('load',function () {
            OWL.load();
        });

        $(document).ready(function(){
            OWL.ajaxComplete();
        });

        var reinitTimer;
        $(window).on('resize', function () {
            clearTimeout(reinitTimer);
            reinitTimer = setTimeout(OWL.resize(), 100);
        });
    }
);
