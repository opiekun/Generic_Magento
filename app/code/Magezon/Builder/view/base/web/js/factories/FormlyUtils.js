define([
    'angular',
    'underscore'
], function(angular, _) {

	var uniqueid = function (size) {
        var code = Math.random() * 25 + 65 | 0,
            idstr = String.fromCharCode(code);

        size = size || 12;

        while (idstr.length < size) {
            code = Math.floor(Math.random() * 42 + 48);

            if (code < 58 || code > 64) {
                idstr += String.fromCharCode(code);
            }
        }

        return idstr.toLowerCase();
    };

    var processFields = function(array, separator, level, key) {
        if (level === undefined) {
            level = 0;
        }
        var types = [];
        var i = 0,length;
        var children = [];
        array  = _.compact(array);
        length = array.length;
        for (i; i < length; i++) {
            var orgiKey = key;
            var row  = array[i];
            var elem = row['config'];
            if (elem['templateOptions']['element']) {
                if (!elem['data']) elem['data'] = {};
                elem['data']['element'] = elem['templateOptions']['element'];
            }
            if (elem['key']) {
                if (key) {
                    key += '.' + elem['key'];
                } else {
                    key = elem['key'];
                }
                elem['key'] = key;
            }
            // Dynamic Rows
            if (row.config.templateOptions.children) {
                key = '';
                row.config.templateOptions.children = this.processFields(row.config.templateOptions.children, separator, level, key);
            }
            if (row.hasOwnProperty(separator)) {
                level++;
                elem['wrapper']    = this.getUniqueId();
                elem['fieldGroup'] = this.processFields(row[separator], separator, level, key);
            } else {
                elem['type'] = this.getUniqueId();
                elem['id']   = this.getUniqueId();
            }

            if (!elem['className']) elem['className'] = '';
            elem['className'] += ' mgz__field';
            elem['className'] += ' mgz_field-' + elem.templateOptions.elementId;
            elem['className'] += ' mgz_field-type-' + elem.templateOptions.builderType;

            if (elem.templateOptions.required) {
                elem['className'] += ' _required';
            }

            if (elem.templateOptions.templateUrl) {
                elem.templateOptions.templateUrl = window.magezonBuilder.viewFileUrl + elem.templateOptions.templateUrl;
            }

            if (elem.templateOptions.wrapperTemplateUrl) {
                elem.templateOptions.wrapperTemplateUrl = window.magezonBuilder.viewFileUrl + elem.templateOptions.wrapperTemplateUrl;
            }

            // overide wrapepr template url
            if (elem.templateOptions.wrapperTemplateUrl) {
                var wrapperId = this.getUniqueId();
                if (!elem['data']) elem['data'] = {};
                elem['data']['wrapperType'] = wrapperId;
                elem['wrapper'] = wrapperId;
            }
            children.push(elem);
            key = orgiKey;
        }
        return children;
    }

    var getComponentDefault = function() {
        return {
            visible: true,
            control: true
        };   
    }

    var getDefaultValues = function(element) {
        var defaultValues        = angular.copy(processDefaultValues(element.tabs, {}));
        defaultValues['type']    = element.type;
        defaultValues['builder'] = getComponentDefault();
        return defaultValues;
    }

    var processDefaultValues = function(fields) {
        var defaultValues = {};
        _.each(fields, function(field, k) {
            if (field.fieldGroup) {
                var _defaultValues = processDefaultValues(field.fieldGroup);
                if (_defaultValues) {
                    if (field.key) {
                        defaultValues[field.key] = _defaultValues;
                    } else {
                        defaultValues = angular.merge(defaultValues, _defaultValues);
                    }
                }
            } else if (field.hasOwnProperty('key') && field.hasOwnProperty('defaultValue')) {
                defaultValues[field.key] = field['defaultValue'];
            }
        });
        return defaultValues;
    };

    return {
        getUniqueId: uniqueid,
        processFields: processFields
    }
});