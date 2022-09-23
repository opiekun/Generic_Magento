var config = {
    map: {
        '*': {
            magezonBuilder: 'Magezon_Builder/js/magezon-builder',
            jarallax: 'Magezon_Builder/js/jarallax/jarallax.min',
            jarallaxVideo: 'Magezon_Builder/js/jarallax/jarallax-video',
            waypoints: 'Magezon_Builder/js/waypoints/jquery.waypoints',
            mgzTabs: 'Magezon_Builder/js/tabs'
        }
    },
    shim: {
        jarallax: {
            exports: 'jarallax',
            deps: ['jquery']
        },
        jarallaxVideo: {
            deps: ['jarallax']
        },
        waypoints: {
            deps: ['jarallax', 'jquery']
        },
        magezonBuilder: {
            deps: ['waypoints', 'mage/bootstrap']
        },
        'Magezon_Builder/js/magezon-builder': {
            deps: ['jquery', 'waypoints', 'mage/bootstrap']
        },
        'Magezon_Builder/js/carousel': {
            deps: ['jquery']
        },
        'Magezon_Builder/js/countdown': {
            deps: ['jquery']
        }
    }
};