define([
	'jquery'
], function($) {

	return {
		controller: function($rootScope, $scope, $timeout, magezonBuilderUrl, profileManager) {
			$scope.text     = 'Save Template';
			$scope.disabled = false;

			$scope.saveTemplate = function() {
				if ($scope.to.saveTemplateUrl) {
					var name = $scope.model[$scope.options.key];
					if (name && !$scope.disabled) {
						$scope.disabled = true;
						$scope.text     = 'Saving';

						var data = {
							name: name,
							profile: profileManager.getShortCode(),
							is_active: 1
						};

						$.ajax({
			                url: magezonBuilderUrl.getUrl($scope.to.saveTemplateUrl),
			                type:'POST',
			                data: data,
			                success: function(res) {
								if (res.message) {
									alert(res.message);
								}
								$scope.$apply(function() {
									$scope.text = 'Saved';
								});
								$timeout(function() {
									$scope.model[$scope.options.key] = '';
									$scope.text                      = 'Save Template';
									$scope.disabled                  = false;
									$rootScope.$broadcast('loadTemplates');
								}, 1000);
			                }
			            });
					}
				}
			}
		}
	}
})