define([
    'jquery',
    'angular'
], function($, angular) {

    return {
        controller: function($rootScope, $scope, magezonBuilderService, magezonBuilderEditor, magezonBuilderFilter) {
            $scope.to.loading = true;
            var config = Object.extend(angular.copy($rootScope.builderConfig.wysiwyg), $scope.to.wysiwyg);
            $scope.id = magezonBuilderService.uniqueid();
            $scope.content = $scope.model[$scope.options.key];
            magezonBuilderEditor.initTinymce($scope.id, config, function(value) {
                $scope.content = $scope.model[$scope.options.key] = magezonBuilderFilter.decodeContent(value);
            }, function() {
                $scope.to.loading = false;
            });
        }
    }
})