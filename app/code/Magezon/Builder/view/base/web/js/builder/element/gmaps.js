define([
	'jquery',
	'angular'
], function($, angular) {

	var directive = function($rootScope, magezonBuilderUrl) {
		return {
			replace: true,
			templateUrl: function(elem) {
				return magezonBuilderUrl.getTemplateUrl(elem, 'Magezon_Builder/js/templates/builder/element/gmaps.html');
			},
			controller: function($scope, $controller) {
				var parent = $controller('baseController', {$scope: $scope});
				angular.extend(this, parent);
			},
			link: function(scope, element) {

				scope.loadElement = function() {
					initGoogleMap();
				}

				function getItems()
				{ 
					var $newItems = [];
					$items        = angular.copy(scope.element.items);
					if ($items) {
						angular.forEach($items, function($_item) {
							var newItem = $_item;
							if (newItem['image']) {
								newItem['image'] = magezonBuilderUrl.getImageUrl($_item['image']);
							}
							$newItems.push(newItem);
						});
					}
					return $newItems;
				}

				function getCenterItem() {
					var $result = '';
					angular.forEach(scope.element.items, function($_item) {
						if ($_item['center'] && $_item['lat'] && $_item['lng'] && !$result) {
							$result = $_item;
						}
					});
					if (!$result) {
						angular.forEach(scope.element.items, function($_item) {
							if ($_item['lat'] && $_item['lng'] && !$result) {
								$result = $_item;
							}
						});
					}
					return $result;
				}

				function initGoogleMap() {
					var _element               = scope.element;
					var uid                    = _element.id + '-map';
					var google_map_zoom        = parseInt(_element.map_zoom);
					var google_map_type        = _element.map_type;
					var google_map_ui          = _element.map_ui;
					var google_map_scrollwheel = _element.map_scrollwheel;
					var google_map_draggable   = _element.map_draggable;
					var items                  = getItems();
					var centerItem             = getCenterItem();
					var self      = this;
					require([
						'https://maps.google.com/maps/api/js?key=' + $rootScope.builderConfig.googleApi + '&libraries=places'
					], function() {
						if (!$('#' + uid).length) return;

						var config = {
							center: {
								lat: parseFloat(centerItem['lat']),
								lng: parseFloat(centerItem['lng'])
							},
							zoom: google_map_zoom,
							mapTypeId: google_map_type,
							disableDefaultUI: google_map_ui,
							scrollwheel: google_map_scrollwheel,
							draggable: google_map_draggable
						};

						var map = new google.maps.Map(document.getElementById(uid), config);

						if (items && items.length) {
							items.each(function(option) {
								var myLatLng = new google.maps.LatLng(option['lat'], option['lng']);
								addMarker(myLatLng, map, option);
							});
						}

						function addMarker(location, map, option) {
							var marker = new google.maps.Marker({
								position: location,
								map: map,
								icon: option['image']
							});
							if (option['info']) {
								var infowindow = new google.maps.InfoWindow({
									content: option['info']
								});
								marker.addListener('click', function() {
									infowindow.open(map, marker);
								});
								if (element.infobox_opened) {
									infowindow.open(map, marker);
								}
							}
						}
					});
				}
				initGoogleMap();
			},
			controllerAs: 'mgz'
		}
	}

	return directive;
});