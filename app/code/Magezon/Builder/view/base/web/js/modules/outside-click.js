require(['jquery'], function($) {

// https://github.com/abrkt/ng-outside-click
(function(){
  'use strict';

  var contains = function (parent, child) {
    var node = child;
    while ((node = node.parentNode) !== null && node !== parent);
    return node !== null;
  };

  angular.module('ngOutsideClick', []).directive('outsideClick', ['$document', function ($document) {
    return {
      restrict: 'A',
      link: function ($scope, $element, $attrs) {
        var scopeExpression = $attrs.outsideClick;
        var onDocumentClick = function (event) {
          if ($element[0] !== event.target && !contains($element[0], event.target) && !$(event.target).parents('.mgz-element-controls').length) {
            $scope.$apply(scopeExpression);
          }
        };
        $document.on('click', onDocumentClick);
        $element.on('$destroy', function () {
          $document.off('click', onDocumentClick);
        });
      }
    };
  }]);

})();

});