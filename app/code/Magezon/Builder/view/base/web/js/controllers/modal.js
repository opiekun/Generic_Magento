define([
	'jquery',
	'angular',
  	'mgzcodemirror'
], function($, angular, CodeMirror) {
	window.mgzCodeMirror = CodeMirror;

	var baseCtrl = function(
		$scope,
		$uibModalInstance,
		magezonBuilderModal,
		modal,
		magezonBuilderService
	) {
		var self = this;
		self.id  = '.' + magezonBuilderModal.getModalId(modal.key);

		$scope.modal    = modal;
		$scope.title    = modal.title ? modal.title : modal.name;
		$scope.subtitle = modal.subtitle;
		var localStorageKey = 'modal.' + modal.key;

		if (magezonBuilderService.getFromLocalStorage(localStorageKey + '.sizes')) $scope.modal.sizes = magezonBuilderService.getFromLocalStorage(localStorageKey + '.sizes');
		if (magezonBuilderService.getFromLocalStorage(localStorageKey + '.position')) {
			var position = magezonBuilderService.getFromLocalStorage(localStorageKey + '.position');
			if (position.top < 0) position.top = 0;
			$scope.modal.position = position;
		}

		self.cancel = function() {
			$uibModalInstance.dismiss('cancel');
		}

		self.getModalSelector = function() {
			return $(self.id);
		}

		self.getModalWidth = function() {
			var windowWidth = $(window).width();
			var modalWidth  = '80%';
			if (modal.size.width) modalWidth = modal.size.width;
			if (angular.isString(modalWidth) && modalWidth.indexOf('%') !== -1) modalWidth = windowWidth / 100 * modalWidth.replace("%", "");
			return modalWidth;
		}

		self.getModalHeight = function() {
			var windowHeight = $(window).height();
			var modalHeight  = '90%';
			if (modal.size.height) modalHeight = modal.size.height;
			if (angular.isString(modalHeight) && modalHeight.indexOf('%') !== -1) modalHeight = windowHeight / 100 * modalHeight.replace("%", "");
			if (modal.position.top && modal.position.bottom) {
				self.getModalSelector().css('max-height', '');
				self.getModalSelector().css('height', '');
			}
			return modalHeight;
		}

		self.getMinHeight = function() {
			return modal.size.minHeight ? modal.size.minHeight : '';
		}

		self.getModalTop = function() {
			var top;
			if (modal.position.top) {
				top = modal.position.top;
			} else {
				top = ($(window).height() - self.getModalHeight()) / 2;
			}
			return top;
		}

		self.getModalRight = function() {
			var right = 'auto';
			if (modal.position.right) {
				right = modal.position.right;
			}
			return right;
		}

		self.getModalBottom = function() {
			var bottom;
			if (modal.position.bottom) {
				bottom = modal.position.bottom;
			}
			return bottom;
		}

		self.getModalLeft = function() {
			var left = 'auto';
			if (modal.position.left) {
				left = modal.position.left;
			} else {
				if (!modal.position.right) {
					left = ($(window).width() - self.getModalWidth()) / 2;
				}
			}
			return left;
		}

		self.getModalMaxWidth = function() {
			var maxWidth;
			if (modal.size.maxWidth) {
				maxWidth = parseFloat(modal.size.maxWidth) + 'px';
			} else {
				maxWidth = parseFloat(modal.size.maxWidth) + 'px';
			}
			return maxWidth;
		}

		self.getHeaderSelector = function() {
			return $(self.id + ' .mgz-modal-header');
		}

		self.getHeaderHeight = function() {
			return self.getHeaderSelector().length ? self.getHeaderSelector().outerHeight() : 0;
		}

		self.getContentSelector = function() {
			return $(self.id + ' .mgz-modal-content');
		}

		self.getFooterSelector = function() {
			return $(self.id + ' .mgz-modal-footer');
		}

		self.getFooterHeight = function() {
			return self.getFooterSelector().length ? self.getFooterSelector().outerHeight() : 0;
		}

		self.isTabsModal = function() {
			return self.getModalSelector().find('.mgz-modal-tab > .tab-content').length ? true : false;
		}

		self.resizeInner = function() {
			var headerHeight = self.getHeaderHeight();
			var footerHeight = self.getFooterHeight();
			var modalHeight  = self.getModalHeight();
			var height       = modalHeight - headerHeight - footerHeight;
			if (self.isTabsModal()) {
				height -= self.getModalSelector().find('.mgz-modal-tab > .nav-tabs').outerHeight();
				self.getModalSelector().find('.mgz-modal-tab > .tab-content').css('max-height', height);
				self.getModalSelector().find('.mgz-modal-content').css('overflow', 'visible');
				self.getModalSelector().addClass('mgz-modal-form');
			} else {
				self.getModalSelector().css('height', modalHeight);
				self.getModalSelector().removeClass('mgz-modal-form');
			}
		}

		$scope.$on('afterLoadModalTabs', function() {
			self.resizeInner();
			setTimeout(function() {
				self.resizeInner();
			}, 500);
		});

		$uibModalInstance.rendered.then(function() {
			if (modal.resizable) {
				var minHeight = 150 + self.getHeaderSelector().outerHeight() + self.getFooterSelector().outerHeight();
				setTimeout(function() {
			    	self.getModalSelector().resizable({
			    		minHeight: minHeight,
			    		minWidth: 380,
			    		handles: "all",
			    		resize: function( event, ui ) {
			    			modal.size.width = ui.size.width;
							if (!self.getModalSelector().hasClass('mgz-minimized')) {
			    				modal.size.height = ui.size.height;
			    			}
			    			self.resizeInner();
			    			magezonBuilderService.saveToLocalStorage(localStorageKey + '.sizes', ui.size);
			    		}
			    	});
				}, 2000);
	    		self.getModalSelector().draggable({
	    			containment: "window",
	    			handle: ".mgz-modal-header",
	    			scroll: false,
	    			drag: function( event, ui ) {
	    				if (modal.position.top >= 0) {
		    				modal.position = ui.position;
				    		magezonBuilderService.saveToLocalStorage(localStorageKey + '.position', ui.position);
				    	}
	    			}
	    		});
	    		self.getHeaderSelector().addClass('mgz-draggable-handle');
		    }
		    self.resizeInner();
		    self.getModalSelector().find('.mgz-modal-tab > .nav.nav-tabs > li > a').click(function(e) {
				self.resizeInner();
			});
			setTimeout(function() {
				self.getModalSelector().find('.mgz-modal-tab > .tab-content').scroll(function(e) {
					$('.sp-active').siblings('.colorpicker-spectrum').spectrum("hide");
				});
			}, 2000);
		});

		self.getModalStyle = function($topModalIndex) {
			var style = {
				'z-index': 800 + $topModalIndex * 10,
				'display': 'block'
			};
			style['width'] = self.getModalWidth();
			if ($scope.modal.form) {
				style['max-height'] = self.getModalHeight();
			} else {
				style['height'] = self.getModalHeight();
			}
			style['top']        = self.getModalTop();
			style['right']      = self.getModalRight();
			style['left']       = self.getModalLeft();
			style['max-width']  = self.getModalMaxWidth();
			style['min-height'] = self.getMinHeight();
			return style;
		}

		$(window).resize(function(event) {
	    	if (self.getModalSelector().is(':visible')) {
	    		self.resizeInner();
	    	}
	    });

	    $scope.$on('enableModalSpinner', function() {
			$scope.spinner = true;
		});

	    $scope.$on('disableModalSpinner', function() {
			$scope.spinner = false;
		});

		return angular.copy(self);
	};

	return baseCtrl;
});