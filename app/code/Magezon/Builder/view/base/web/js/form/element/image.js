define([
	'angular',
	'Magezon_Core/js/mage/browser'
], function(angular) {

	return {
		controller: function($scope, $rootScope) {
			$scope.openFileManager =  function() {
	            var fileManagerUrl = $rootScope.builderConfig.fileManagerUrl.replace('UID', $scope.id);
	            MgzMediabrowserUtility.openDialog(fileManagerUrl);
	        }

	        $scope.getSrc = function() {
	        	if ($scope.model[$scope.options.key]) {
					return $rootScope.builderConfig.mediaUrl + $scope.model[$scope.options.key];
				}
	        }
		}
	}
})