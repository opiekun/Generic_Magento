define([
	'jquery',
	'underscore',
	'angular',
	'Magezon_Builder/js/apply/main',
	'Magezon_Builder/js/parallax'
], function($, _, angular, mage, parallax) {

	var isMobile = /Android|iPhone|iPad|iPod|BlackBerry|Windows Phone/g.test(navigator.userAgent || navigator.vendor || window.opera);

	var baseCtrl = function(
		$scope,
		$rootScope,
		$timeout,
		magezonBuilderService,
		magezonBuilderUrl,
		magezonBuilderFilter,
		elementManager,
		$sce,
		$interpolate,
		magezonBuilderModal,
		$compile
	) {

		var self = this;
		var element = $scope.element;

		self.getBuilderElement = function(type) {
			return elementManager.getElement(type);
		}

		$scope.controls            = [];
		$scope.controlsTemplateUrl = magezonBuilderUrl.getViewFileUrl('Magezon_Builder/js/templates/builder/controls.html');
		$scope.headingTypes        = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
		$scope.alignTypes          = ['left', 'center', 'right'];
		$scope.loaded              = false;
		$scope.toolbarLoaded       = false;
		$scope.toolbar             = true;
		$scope.resizableLoaded     = false;
		$scope.resizable           = true;
		$scope.addBlock            = true;

		$scope.$on('loadElements', function(event, elem) {
			self.afterRender();
		});
		$scope.loadElement = function() {}

		$scope.getEl = function() {
			return elementManager.getEl(element);
		}

		self.isProfile = function() {
			if (!element.id || element.type == 'profile') {
				return true;
			}
			return false;
		}

		$scope.getElemIndex = function(elem) {
			return _.findIndex(element.elements, function(_elem) {
				return _elem.id == elem.id;
			});
		}

		$scope.isChildren = function(elem) {
			var index = $scope.getElemIndex(elem);
			return (index !== -1) ? true : false;
		}

		self.onDragstart = function(elem) {
			$rootScope.draggingElement = elem;
			$rootScope.$broadcast('onDragstart', elem);
		}

		self.onDragend = function(elem) {
			$rootScope.draggingElement = '';
			$scope.controls = [];
			$rootScope.$broadcast('onDragend', elem);
			$('.mgz-element-collection-dragover').removeClass('mgz-element-collection-dragover');
		}

		self.onCanceled = function(elem) {
			$rootScope.draggingElement = '';
			$scope.controls = [];
			$rootScope.$broadcast('onCanceled', elem);
		}

		self.onMoved = function(elem) {
			$rootScope.draggingElement = '';
			$scope.controls = [];
			$rootScope.$broadcast('onMoved', elem);
		}

		self.getWrapperClasses = function() {
			var classes = [];
			if (element.animation_in) {
				classes.push('mgz-animated');
				classes.push(element.animation_in);
				// classes.push('mgz_start_animation');
				// classes.push('animated');
				if (element.animation_infinite) {
					classes.push('mgz-animated-infinite');
				}
			}
			if (element.el_class) classes.push(element.el_class);
			if (element.hidden_default) classes.push('mgz-element-hide-default');
			if (element.title_align) classes.push('mgz-element-title-align-' + element.title_align);
			if (element.el_float) {
				var _float = magezonBuilderService.getDesignValue(element, 'el_float');
				if (_float) classes.push('f-' + _float);
			}
			classes.push(element.id);
			classes.push('mgz-element');
			if (!element.builder.is_collection) classes.push('mgz-child');
			classes.push('mgz-element-' + element.type);
			if (element.builder.is_collection) classes.push('mgz-element-collection');
			if (element.builder.is_collection && !element.elements.length) {
				classes.push('mgz-element-empty');
			}
			if (element.disable_element) classes.push('mgz-element-disabled');
			if (element.builder.actived || ($rootScope.editingElement && element.id == $rootScope.editingElement.id)) classes.push('mgz-element-actived');
			if (element.builder.editing) classes.push('mgz-element-editing');
			if (element.builder.hovered) classes.push('mgz-element-hover');
			if (element.elements && element.elements.length) {
				classes.push('has-children');	
			}
			var viewMode = magezonBuilderService.getViewMode();
			if (element[viewMode + '_hide']) classes.push('mgz-hidden');
			classes = classes.concat(self.getViewModeClass(element));
			if (element.builder.additionalClasses) {
				classes = classes.concat(element.builder.additionalClasses);
			}
			return classes;
		}

		self.getInnerClasses = function() {
			var classes = [];
			classes.push('mgz-element-inner');
			classes.push(self.getStyleHtmlId());
			if (element.el_inner_class) classes.push(element.el_inner_class);
			return classes;
		}

		self.getViewModeClass = function(elem) {
			var classes = [];
			if (elem.builder && elem.builder.resizable) {
				var responsiveClass = magezonBuilderService.getResponiveValue(elem, 'size');
				if (responsiveClass) {
					classes.push('mgz-col-xs-' + responsiveClass);
				} else {
					classes.push('mgz-col-xs-12');
				}
				var modeOffset = magezonBuilderService.getResponiveValue(elem,'offset_size');
				if (modeOffset) classes.push('mgz-col-xs-offset-' +  modeOffset);
			}
			return classes;
		}

		$scope.getTrustedHtml = function(html) {
			return $sce.trustAsHtml(magezonBuilderFilter.encodeContent(html));
		}

		$scope.trustAsResourceUrl = function(url) {
			return $sce.valueOf($sce.trustAsResourceUrl(url));
		}

		$scope.getBuilderElementDescription = function() {
			var builderElement = element.builder;
			return builderElement.builder_description ? $interpolate(builderElement.builder_description)($scope) : '';
		}

		$scope.getPlaceHolderClassess = function() {
			var classes;
			if ($rootScope.draggingElement) {
				var elem = $rootScope.draggingElement;
				var classes = self.getViewModeClass(elem);
				if (elem.el_float) classes += ' f-' + elem.el_float;
			}
			return classes;
		}

		$scope.getTitleHtml = function() {
			if (!element.title_tag) element.title_tag = 'h4';
				var html = '<' + element.title_tag + ' class="title">';
			if (element.add_icon && element.icon_position == 'left') {
				html += '<i class="mgz-icon-element ' + element.icon + '"></i>';
			}
			if (element.title) {
				html += '<span>' + element.title + '</span>';
			}
			if (element.add_icon && element.icon_position == 'right') {
				html += '<i class="mgz-icon-element ' + element.icon + '"></i>';
			}
			html += '</' + element.title_tag + '>';
			return html;
		}

		self.getParallaxId = function() {
			return element.id ? element.id + '-p' : '';
		}

		self.getStyleHtmlId = function() {
			return element.id ? element.id + '-s' : '';
		}

		self.onMouseEnter = function(e) {
			if (!$rootScope.draggingElement || ($('.mgz-element-editing').length && $('.mgz-element-editing') !== $scope.getEl())) {
				if (!$scope.toolbarLoaded && $scope.toolbar) {
					var html = $compile('<magezon-builder-element-actions></magezon-builder-element-actions>')($scope);
					$scope.getEl().prepend(html);
					$scope.toolbarLoaded = true;
				}
				if (!$scope.resizableLoaded && $scope.resizable && element.builder.resizable) {
					var html = $compile('<magezon-builder-element-resizable></magezon-builder-element-resizable>')($scope);
					$scope.getEl().append(html);
					$scope.resizableLoaded = true;
				}

				element.builder.hovered = true;
				var target = $(e.currentTarget);
				$scope.controls = self.getControls();
				angular.forEach($scope.controls, function(elem, index) {
					elem.builder.controlsVisible = false;
				});
				$timeout(function() {
					var controlSelector = target.children('.mgz-element-wrap-top').children('.mgz-element-controls');
					if (target.find('.mgz-element-hover').length) {
						element.builder.controlsVisible = false;
					} else {
						element.builder.controlsVisible = true;
					}
					setTimeout(function() {
						if (controlSelector.length) {
							var navSelector    = $('#' + $rootScope.builderConfig.htmlId + ' .mgz-navbar');
							if (navSelector.length) {
								var navOffsetRight = navSelector.offset().left + navSelector.width();
								var controlSelectorOffsetRight = controlSelector.offset().left + controlSelector.width() + 2;
								if (controlSelectorOffsetRight > navOffsetRight) {
									controlSelector.css('right', '0');
									controlSelector.css('left', 'auto');
								} else {
									controlSelector.css('right', 'auto');
									if (target.css('position') == 'static') {
										controlSelector.css('left', target.position().left);
									} else {
										controlSelector.css('left', '0');
									}
								}
							}
						}
					});
				}, 200);
			}
		}

		self.onMouseLeave = function(e) {
			element.builder.hovered = false;
			if (!$rootScope.draggingElement) {
				var target = $(e.currentTarget);
				$scope.controls = [];
				element.builder.controlsVisible = false;
			}
		}

		self.getParents = function() {
			var parents = {};
			var prepareParents = function(_scope, _parents) {
				if (_scope.$parent) {
					var _element = _scope.element;
					if (_element && _element.id && !_parents.hasOwnProperty(_element.id)) {
						_parents[_element.id] = _element;
					}
					prepareParents(_scope.$parent, _parents);
				}
			}
			prepareParents($scope.$parent, parents);
			return _.values(parents).reverse();
		}

		self.getControls = function() {
			var parents = self.getParents();
			return parents;
		}

		self.getControlClasses = function(elem, $last) {
			var classes = [];
			classes.push('mgz-element-control');
			classes.push('mgz-element-' + elem.type + '-control');
			if ($last) classes.push('mgz-element-control-last mgz-element-control-green');
			return classes;
		}

		$scope.$on('editElement', function($e, elem, activeTab) {
			if (elem.id == element.id && !$rootScope.editingElement) {
				self.editElement(elem, activeTab);
				$rootScope.$broadcast('elementReloaded', element);
			}
		})

		self.editElement = function(elem, activeTab) {
			elem.builder.editing = false;
			elem.builder.hovered = false;
			$rootScope.editingElement = elem;
			var modal = magezonBuilderModal.open('element', {
				windowClass: 'mgz-modal-' + elem.type,
				resolve: {
					form: {
						element: elem,
						activeTab: activeTab
					}
				}
			}, function() {
				self.deactiveElement(elem);
				$rootScope.editingElement = '';
			}, function() {
				self.deactiveElement(elem);
				$rootScope.editingElement = '';
			});
		}

		self.dropElement = function(elem, index, parent) {
			$rootScope.$broadcast('removeElement', elem, {history: false});
			$rootScope.$broadcast('beforeDropElement', elem);
			$scope.$apply(function() {
				parent.elements.splice(index, 0, elem);
				self.deactiveElement(elem);
				$rootScope.$broadcast('addHistory', {
					type: 'moved',
					title: elem.builder.name
				});
			});
			self.onDragend();
			$rootScope.$broadcast('afterDropElement', elem);
			return true;
		}

		$scope.$on('activeElement', function(event, elem) {
			if (elem.id == element.id) {
				self.activeElement(element);
			} else {
				element.builder.actived = false;
			}
		});

		self.activeElement = function(elem) {
			elem.builder.actived = true;
			$rootScope.activedElement = elem;
		}

		$scope.$on('deactiveElement', function(event, elem) {
			self.deactiveElement(element);
		});

		self.deactiveElement = function(elem) {
			elem.builder.actived = false;
			$rootScope.activedElement = '';
		}

		$scope.$on('gotoElement', function(event, elem) {
			if (elem.id == element.id) {
				var height = $('.page-main-actions > .page-actions._fixed').outerHeight() + 5;
				var _elem  = $(".mgz-builder .mgz-element." + element.id);
				if (_elem.is(':visible')) {
					$('html, body').stop().animate({
						scrollTop: _elem.offset().top - height
					}, 1000);
				}
			}
		});

		$scope.removeSpinner = function() {
			$scope.getEl().children('.mgz-spinner').remove();
		}

		var addAddBlock = function() {
			if ($scope.addBlock && !self.isProfile()) {
				var _elem = element;
				if (_elem.builder.is_collection) {
					$scope.removeSpinner();
					if (!_elem.elements.length) {
						var html = $compile('<i class="mgz-icon mgz-icon-add" ng-if="element.builder.is_collection&&!element.elements.length" ng-click="$root.$broadcast(\'addElement\', {elem: element, action: \'append\' })"></i>')($scope);
						$scope.getEl().prepend(html);
					}
				}
			}
		}

		$scope.addAddBlock = function() {
			addAddBlock();
			if ($scope.getEl().find('.mgz-icon-add')) {
				setTimeout(function() {
					addAddBlock();
				}, 500);
			}
		}

		self.loadLiveElement = function(elem) {
			if (elem.builder.livePreview) {
				magezonBuilderService.elemPost(elem, $rootScope.builderConfig.loadElementUrl, {
					element: angular.toJson(elem)
				}, false, function(res) {
					var _elem = $('.' + elem.id);
					var _inner = _elem.children('.mgz-element-inner');
					if (res) {
						_inner.html(res);
						$(mage.apply);
						_inner.on('click', '.action.tocart,.action.towishlist,.action.tocompare', function (e) {
							return false;
						});
						_inner.find('a').attr('target', '_blank');
						_inner.find('form[data-role=tocart-form]').removeAttr('action');
						_inner.find('form[data-role=tocart-form]').removeAttr('method');
						_inner.find('*').addClass('mgz-builder-dnd-disable');
						$('body').trigger('magezonPageBuilderUpdated');
						setTimeout(function() {
							_elem.find('.mgz-waypoint').trigger('mgz:animation:run');
						}, 1000);
						$rootScope.$broadcast('loadElements');
					}
				}, function() {}, function() {
					$scope.removeSpinner();
				});
			}
		}
		setTimeout(function() {
			self.loadLiveElement(element);	
		});

		self.getParallaxClasses = function() {
			var parallax      = magezonBuilderService.getDesignValue(element, 'parallax_type');
			var mouseParallax = magezonBuilderService.getDesignValue(element, 'mouse_parallax');
			var classes  = [];
			classes.push('mgz-parallax');
			classes.push(self.getParallaxId());
			if (parallax && mouseParallax) classes.push('mgz-parallax-mouse-parallax');
			return classes;
		}

		self.afterParallaxRender = function(e) {
			setTimeout(function() {
				self.initParallax();
			}, 1000);
		}

		self.initParallax = function() {
			var parallax   = magezonBuilderService.getDesignValue(element, 'parallax_type');
			var elParallax = $scope.getEl().children('.mgz-element-inner').children('.mgz-parallax').children('.mgz-parallax-inner');

			jarallax(elParallax, 'destroy');
			if (parallax) {
				elParallax.css('background-image', 'none');	
			} else {
				elParallax.css('background-image', '');	
			}
			if (!self.isEnabledParallax()) return;
			var _elem           = element;
			var backgroundType  = magezonBuilderService.getDesignValue(_elem, 'background_type');
			var imageBgSize     = '';
			var imageBgPosition = magezonBuilderService.getDesignValue(_elem, 'background_position').replace(/-/g, ' ');
			var video           = false;
			var videoStartTime  = 0;
			var videoEndTime    = 0;
			var videoVolume     = 0;
			var videoLoop       = true;
			var videoAlwaysPlay = true;
			var videoMobile     = false;
			var parallax        = magezonBuilderService.getDesignValue(_elem, 'parallax_type');
			var parallaxSpeed   = magezonBuilderService.getDesignValue(_elem, 'parallax_speed');
			var parallaxMobile  = magezonBuilderService.getDesignValue(_elem, 'parallax_mobile');

	        // video type
	        if ( backgroundType === 'yt_vm_video' || backgroundType === 'video' ) {
	        	video           = magezonBuilderService.getDesignValue(_elem, 'background_video');
	        	videoStartTime  = parseFloat( magezonBuilderService.getDesignValue(_elem, 'video_start_time') ) || 0;
	        	videoEndTime    = parseFloat( magezonBuilderService.getDesignValue(_elem, 'video_end_time') ) || 0;
	        	videoVolume     = parseFloat( magezonBuilderService.getDesignValue(_elem, 'video_volume') ) || 0;
	        	videoLoop       = true;
	        	videoAlwaysPlay = true;
	        	videoMobile     = magezonBuilderService.getDesignValue(_elem, 'video_mobile');

	        	if ( video && ! parallax && ! parallaxSpeed ) {
	        		parallax       = 'scroll';
	        		parallaxSpeed  = 1;
	        		parallaxMobile = videoMobile;
	        	}
	        }

	        // prevent if no parallax and no video
	        if ( !parallax && !video ) return;

	        var jarallaxParams = {
	        	automaticResize: true,
	        	type: parallax,
	        	speed: parallaxSpeed,
	        	disableParallax() {
	        		return parallaxMobile ? false : isMobile;
	        	},
	        	disableVideo() {
	        		return videoMobile ? false : isMobile;
	        	},
	        	imgSize: imageBgSize || 'cover',
	        	imgPosition: imageBgPosition || '50% 50%',
	        };

	        if ( imageBgSize === 'pattern' ) {
	        	jarallaxParams.imgSize   = 'auto';
	        	jarallaxParams.imgRepeat = 'repeat';
	        }

	        if ( video ) {
				jarallaxParams.speed                = parallax ? parallaxSpeed : 1;
				jarallaxParams.videoSrc             = video;
				jarallaxParams.videoStartTime       = videoStartTime;
				jarallaxParams.videoEndTime         = videoEndTime;
				jarallaxParams.videoVolume          = videoVolume;
				jarallaxParams.videoLoop            = videoLoop;
				jarallaxParams.videoPlayOnlyVisible = !videoAlwaysPlay;
	        }

	        var backgroundImage = magezonBuilderService.getDesignValue(element, 'background_image');
	        if (backgroundImage) jarallaxParams.imgSrc = magezonBuilderUrl.getImageUrl(backgroundImage);
	        var elParallax = $scope.getEl().children('.mgz-element-inner').children('.mgz-parallax').children('.mgz-parallax-inner');
	        jarallax(elParallax, jarallaxParams);
	        window.mgzParallaxMouse(true);
		}

		self.processParallax = function() {
			$scope.getEl().children('.mgz-element-inner').children('.mgz-parallax').remove();
			if (self.isEnabledParallax()) {
				var html = $compile(self.getParallaxHtml())($scope);
				$scope.getEl().children('.mgz-element-inner').prepend(html);
			}
		}

		$scope.$on('editedElement', function(event, elem) {
			if (element.id === elem.id) {
				self.afterRender();
			}
		});

		$scope.$on('resetElement', function(event) {
			element.builder.hovered = false;
			element.builder.editing = false;
			element.builder.actived = false;
		});

		self.isEnabledParallax = function() {
			var _elem           = element;
			var parallax        = magezonBuilderService.getDesignValue(_elem, 'parallax_type');
			var backgroundType  = magezonBuilderService.getDesignValue(_elem, 'background_type');
			var backgroundImage = magezonBuilderService.getDesignValue(_elem, 'background_image');
			var backgroundColor = magezonBuilderService.getDesignValue(_elem, 'background_color');
			var video           = magezonBuilderService.getDesignValue(_elem, 'background_video');
			return (backgroundColor || backgroundImage || (backgroundType === 'yt_vm_video' && video));
		}

		self.processWaypoints = function() {
			$scope.getEl().waypoint(function() {
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

		self.afterRender = function() {
			$scope.loadElement();
			self.processParallax();
			self.processWaypoints();
			$scope.loaded = true;
			$rootScope.$broadcast('elementReloaded', element);
			if (element.el_id) {
				$scope.getEl().attr('id', element.el_id);
			} else {
				$scope.getEl().removeAttr('id');
			}
		}

		self.getStyles = function() {
			var _elem = element;
			var styles = {};
			if (_elem.animation_in && _elem.animation_duration) {
				styles['animation-duration'] = _elem.animation_duration + 's';
			}
			return styles;
		}

		self.getParallaxHtml = function() {
			return '<div ng-if="mgz.isEnabledParallax()" ng-class="mgz.getParallaxClasses()" data-parallax-mouse-parallax-size="{{ $root.magezonBuilderService.getDesignValue(element, \'mouse_parallax_size\') }}" data-parallax-mouse-parallax-speed="{{ $root.magezonBuilderService.getDesignValue(element, \'mouse_parallax_speed\') }}"><div class="mgz-parallax-inner" after-render="mgz.afterParallaxRender()"></div></div>';
		}

		$scope.addHistory = function(type, subtitle) {
			$rootScope.$broadcast('addHistory', {
				type: type,
				title: element.builder.name,
				subtitle: subtitle
			});
		}

		$scope.isLoaded = function() {
			return $scope.loaded;
		}

		$scope.getDesignValue = function(key) {
			return magezonBuilderService.getDesignValue(element, key);	
		}

		$timeout(function() {
			$scope.loaded = true;
			self.afterRender();
		}, 500);

		setTimeout(function() {
			window.mgzParallaxMouse(true);
		}, 3000);
		
		return angular.copy(this);
	};

	return baseCtrl;
});