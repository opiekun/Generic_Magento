define([
	'jquery',
	'angular'
], function($, angular) {

	var directive = function(magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/modal/block/elements_search.html');
			},
			controller: function($scope) {
				var $parent = $scope.$parent;

				$scope.filterElements = function() {
					var q = $parent.mgz.search;
					$parent.mgz.active = 0;
					$parent.mgz.tabs['all']['elements'] = _.filter(angular.copy($parent.mgz.allTab.elements), function(element) {
						var description = element.description;
						var search = element.search;
						var group = element.group;
						return (element.name.toLowerCase().indexOf(q.toLowerCase()) !== -1) 
						|| (description && description.toLowerCase().indexOf(q.toLowerCase()) !== -1) 
						|| (search && search.toLowerCase().indexOf(q.toLowerCase()) !== -1
						|| (group && group.toLowerCase().indexOf(q.toLowerCase()) !== -1))
					});
				}

				$scope.clearSearch = function() {
					$('.mgz-elements-filter').trigger('focus');
					$parent.mgz.clearSearch();
				}
				$('.mgz-elements-filter').trigger('focus');
			}
		}
	}

	return directive;
});