 var config = {
    "map": {
        '*': {
            button: 'Magezon_TabGrid/js/button'
        }
    },
    "config": {
        "mixins": {
            "jquery/jstree/jquery.jstree": {
                "Magezon_TabGrid/js/tree-mixin": true
            }
        }
    },
    "deps": [
        "Magezon_TabGrid/js/globals"
    ],
    "shim": {
        'Magezon_TabGrid/js/tabs': {
            "deps": ['jquery']
        }
    }
};