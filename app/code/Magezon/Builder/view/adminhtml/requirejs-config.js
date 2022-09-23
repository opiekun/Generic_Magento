var config = {
    map: {
        '*': {
            jarallax: 'Magezon_Builder/js/jarallax/jarallax.min',
            jarallaxVideo: 'Magezon_Builder/js/jarallax/jarallax-video',
            waypoints: 'Magezon_Builder/js/waypoints/jquery.waypoints',
        }
    },
    paths: {
        angular: 'Magezon_Builder/vendor/angular/angular',
        dndLists: 'Magezon_Builder/vendor/angular-drag-and-drop-lists/angular-drag-and-drop-lists',
        magezonBuilder: 'Magezon_Builder/js/builder',
        formly: 'Magezon_Builder/vendor/angular-formly/dist/formly',
        uiBootstrap: 'Magezon_Builder/js/ui-bootstrap-tpls-2.5.0.min',
        'api-check': 'Magezon_Builder/vendor/api-check/dist/api-check',
        formlyUtils: 'Magezon_Builder/js/factories/FormlyUtils',
        angularSanitize : 'Magezon_Builder/vendor/angular-sanitize/angular-sanitize',
        dynamicDirective: 'Magezon_Builder/js/modules/dynamicDirective',
        outsideClickDirective: 'Magezon_Builder/js/modules/outside-click',
        owlcarouselDirective: 'Magezon_Builder/js/modules/angular-owl-carousel-2',
        mgzcodemirror: 'Magezon_Builder/vendor/codemirror/lib/codemirror',
        codemirrorCss: 'Magezon_Builder/vendor/codemirror/mode/css/css',
        uiCodemirror: 'Magezon_Builder/vendor/angular-ui-codemirror/ui-codemirror',
        uiSelect: 'Magezon_Builder/vendor/angular-ui-select/dist/select.min',
        ngStats: 'Magezon_Builder/vendor/ng-stats/ng-stats',
        mgzspectrum: 'Magezon_Builder/vendor/spectrum/spectrum',
        mgztinycolor: 'Magezon_Builder/vendor/spectrum/tinycolor',
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
        angular: {
            exports: 'angular'
        },
        dndLists: {
            deps: ['angular']
        },
        uiBootstrap: {
            deps: ['angular']
        },
        angularSanitize: {
            deps: ['angular']
        },
        dynamicDirective: {
            deps: ['angular']
        },
        outsideClickDirective: {
            deps: ['angular']
        },
        owlcarouselDirective: {
            deps: ['angular']
        },
        mgzUiTinymce: {
            deps: ['angular']
        },
        codemirror: {
            exports: 'CodeMirror'
        },
        uiCodemirror: {
            deps: ['mgzcodemirror', 'angular']
        },
        uiSelect: {
            deps: ['angular']
        },
        ngStats: {
            deps: ['angular']
        },
        staticInclude: {
            deps: ['angular']
        },
        formly: {
            deps: ['jquery']
        },
        'Magezon_Builder/js/carousel': {
            deps: ['jquery']
        },
        'Magezon_Builder/js/countdown': {
            deps: ['jquery']
        }
    }
};