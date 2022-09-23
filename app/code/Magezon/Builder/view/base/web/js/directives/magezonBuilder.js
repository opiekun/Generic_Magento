define([
	'jquery',
	'waypoints'
], function ($) {

	var magezonBuilderDir = function(profileManager, $timeout, magezonBuilderUrl, $templateRequest) {
		return {
			scope: {
				profile: '='
			},
    		replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/builder.html')
			},
			controller: function($rootScope, $scope, magezonBuilderModal, historyManager, magezonBuilderService, magezonBuilderConfig) {
				$rootScope.builderId = magezonBuilderService.getUniqueId();

				$scope.$on('openElementsModal', function(event) {
					$scope.openModal();
				});

				$scope.openModal = function() {
					magezonBuilderModal.open('elements').result.then(function() {}, function() {
						magezonBuilderModal.setElement(null);
						magezonBuilderModal.setAction(null);
						magezonBuilderModal.setData(null);
						magezonBuilderModal.setOpenModal(null);
					});
				}

				$rootScope.$on('loadStyles', function(e, draggingElement) {
					$scope.loadStyles();
				});

		        $scope.loadStyles = function() {
		        	if ($rootScope.builderConfig.loadStylesUrl && $rootScope.profile.elements && $rootScope.profile.elements.length) {
		        		magezonBuilderService.post($rootScope.builderConfig.loadStylesUrl, {
		        			profile: profileManager.toString()
		        		}, true, function(res) {
		        			if (res.message) {
		        				alert(res.message);
		        			}
		        			if (res.status) {
		        				$('#' + $rootScope.builderConfig.targetId + '-styles').html(res.html);
		        			}
		        		});
			        }
		        }

		        $templateRequest(magezonBuilderUrl.getViewFileUrl('Magezon_Builder/js/templates/navigator/element/list.html')).then(function(html) {
		        	$timeout(function() {
						$('.' + $rootScope.builderConfig.htmlId + '-spinner').remove();
						if (profileManager.getKey()) {
							if (magezonBuilderService.isJSON(profileManager.getContent())) {
								$('.' + $rootScope.rootId).addClass('mgz-deactive-builder');
								$('.' + $rootScope.rootId).removeClass('mgz-active-builder');
								$rootScope.$broadcast('importShortcode');
							} else {
								if (profileManager.getContent()) {
									$('.' + $rootScope.rootId).addClass('mgz-active-builder');
									$('.' + $rootScope.rootId).removeClass('mgz-deactive-builder');
								} else {
									$('.' + $rootScope.rootId).addClass('mgz-deactive-builder');
									$('.' + $rootScope.rootId).removeClass('mgz-active-builder');
								}
							}
						} else {
							$rootScope.$broadcast('importShortcode');
						}
			        }, 100);
		        });

		        $scope.$on('addElement', function(e, item) {
		        	var elem, action, openModal, data, type;
		        	if (item && item.hasOwnProperty('elem')) elem = item.elem;
		        	if (item && item.hasOwnProperty('action')) action = item.action;
		        	if (item && item.hasOwnProperty('openModal')) openModal = item.openModal;
		        	if (item && item.hasOwnProperty('data')) data = item.data;
		        	if (item && item.hasOwnProperty('type')) type = item.type;
		        	if (type) {
		        		$rootScope.$broadcast('addNewElment', {
							elem: elem,
							type: type,
							action: 'append',
							openModal: openModal,
							data: data
						});
		        	} else {
		        		if (elem) magezonBuilderModal.setElement(elem);
						if (action) magezonBuilderModal.setAction(action);
						if (openModal) magezonBuilderModal.setOpenModal(openModal);
						if (data) magezonBuilderModal.setData(data);
						$rootScope.$broadcast('openElementsModal');
		        	}
				});

				var loadAnimation = function() {
					if ($(".mgz-animated:not(.mgz_start_animation)").length) {
						$(".mgz-animated:not(.mgz_start_animation)").waypoint(function() {
							var self = this;
							var delayTime = 0;
							if ($(this.element).data('animation-delay')) {
								delayTime = $(this.element).data('animation-delay');
							}
							var durationTime = 0;
							if ($(this.element).data('animation-duration')) {
								durationTime = $(this.element).data('animation-duration');
							}
							if (durationTime) $(self.element).css("animation-duration", durationTime + 's');

							setTimeout(function() {
								$(self.element).addClass("mgz_start_animation animated")
							}, delayTime * 1000);
						}, {
							offset: "85%"
						});
					}
				}

				$scope.$on('editedElement', function(e, elem) {
					loadAnimation();
				});
			}
		}
	};

	return magezonBuilderDir;
});