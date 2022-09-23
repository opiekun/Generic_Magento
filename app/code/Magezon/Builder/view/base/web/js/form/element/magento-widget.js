define([
	'jquery'
], function($) {

	return {
		controller: function($rootScope, $scope, magezonBuilderService, magezonBuilderFilter, $sce) {
			$scope.ajax = true;
			$rootScope.$broadcast('disableSaveBtn');
			magezonBuilderService.post('mgzbuilder/widget/index', {}, false, function(res) {
				$scope.$apply(function() {
					$scope.html = $sce.trustAsHtml(res);
					$rootScope.$broadcast('enableSaveBtn');
				});
			});

			$scope.$watch('html', function(value) {
				if ($scope.model[$scope.options.key] && value) {
					var widgetCode   = angular.copy($scope.model[$scope.options.key]);
					var optionValues = new Hash({});
					var widgetValue;

					// mage/adminhtml/wysiwyg/widget.js - line 287
	                widgetCode.gsub(/([a-z0-9\_]+)\s*\=\s*[\"]{1}([^\"]+)[\"]{1}/i, function (match) {
	                    if (match[1] == 'type') { //eslint-disable-line eqeqeq
	                        widgetValue = match[2];
	                    } else {
	                        optionValues.set(match[1], match[2]);
	                    }
	                });

	                if (widgetValue) {
	                	$rootScope.$broadcast('disableSaveBtn');
	                	var params = {
			                'widget_type': widgetValue,
			                values: optionValues
			            };
			            magezonBuilderService.post('mgzbuilder/widget/loadOptions', {
			            	widget: Object.toJSON(params)
			            }, false, function(res) {
			            	$rootScope.$broadcast('enableSaveBtn');
			            	var widgetCode = widgetValue.gsub(/\//, '_');
			            	$('#select_widget_type').val(widgetCode);
			            	var optionsContainerId = 'widget_options_' + widgetCode;
			            	var optionsContainer   = $('#' + optionsContainerId);
			            	if (optionsContainer.length) {
			            		optionsContainer.html(res);
			            	} else {
			            		var html = '<div id="' + optionsContainerId + '">';
			            		html += res;
			            		html += '</div>';
			            		$('#widget_options').append(html);
			            	}
			            });
	                }
				}
			});

			$scope.updateValue = function() {
				var form  = $('#widget_options_form');
				var valid = true;
				form.find('.error').each(function(index, el) {
					if ($(this).is(':visible')) {
						valid = false;
					}
				});
				if (form.valid() && $scope.ajax) {
					$scope.ajax = false;
					$rootScope.$broadcast('disableSaveBtn');
					magezonBuilderService.post('mgzbuilder/widget/buildWidget', form.serialize(), false, function(res) {
						$scope.$apply(function() {
							$scope.ajax = true;
							$rootScope.$broadcast('enableSaveBtn');
							$scope.model[$scope.options.key] = magezonBuilderFilter.decodeContent(res);
						});
					});
				}
			}

			$(document).on('click', '.rule-param-apply,.rule-param-remove,.data-grid ._clickable,#select_widget_type,.x-tree-node-el', function() {
				$scope.updateValue();
			});

			$(document).on('change', "#widget_options_form *[name^='parameters'],#select_widget_type", function() {
				if ($(this).parents('.mgz-modal-form').length) {
	               $scope.updateValue();
				}
			});
		}
	}
});