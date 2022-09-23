var config = {
    map: {
        '*': {
            owl_carousel: 'WeltPixel_OwlCarouselSlider/js/owl.carousel',
            owl_config: 'WeltPixel_OwlCarouselSlider/js/owl.config',
            owlAjax: 'WeltPixel_OwlCarouselSlider/js/owlAjax'
        }
    },
    shim: {
        owl_carousel: {
            deps: ['jquery']
        },
        owl_config: {
            deps: ['jquery','owl_carousel']
        },
        owlAjax: {
            deps: ['jquery','owl_carousel', 'owl_config']
        }
    }
};