/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
var config = {
    map: {
        "*": {
            cubeportfolio: 'FME_Photogallery/js/cubeportfoliojs',
            main: 'FME_Photogallery/js/main',
            jqueryconf: 'FME_Photogallery/js/jqueryconfy',
            jqueryconf11: 'FME_Photogallery/js/upgrade/jquery-1.11.1.min',
            owlcarousel: 'FME_Photogallery/js/owlcarousel',
            jqueryfunction: 'FME_Photogallery/js/jqueryfunction',
            shadowbox: 'FME_Photogallery/js/shadowbox',
            finaltilesgallery: 'FME_Photogallery/js/upgrade/jquery.finaltilesgallery',
            finalmagpop: 'FME_Photogallery/js/upgrade/jquery.magnific-popup.min',
            photolighbox: 'FME_Photogallery/js/upgrade/lightbox2',
            tdboxslidder: 'FME_Photogallery/js/upgrade2/3d/box-slider.jquery',
            cloud9: 'FME_Photogallery/js/upgrade2/cloud9/jquery.cloud9carousel',
            lightboxcolor: 'FME_Photogallery/js/mensory/lightbox',
            nanogallery2: 'FME_Photogallery/js/nano/jquery.nanogallery2',
            ugallery : 'FME_Photogallery/js/upgrade/unitegallery',
            uthemegrid: 'FME_Photogallery/js/upgrade/ug-theme-grid'
        }
    },
    paths: {
        "cubeportfolio": 'js/cubeportfoliojs',
        "main": 'js/main',
        "jqueryconf": 'js/jqueryconfy',
        "owlcarousel": 'js/owlcarousel/owlcarousel',
        "jqueryfunction": 'js/jqueryfunction',
        "shadowbox": 'js/shadowbox'
    },
    shim: {
        "cubeportfolio": {
            deps: ["jquery"]
        },
        "main": {
            deps: ["cubeportfoliojs"]
        },
        "shadowbox": {
            deps: ["jquery"]
        },
        "jqueryfunction": {
            deps: ["owlcarousel"]
        },
        "jqueryfunction": {
            deps: ["jquery"]
        },
        "owlcarousel": {
            deps: ["jquery"]
        },
        "jqueryconf": {
            deps: ["jquery"]
        }
    }
};
