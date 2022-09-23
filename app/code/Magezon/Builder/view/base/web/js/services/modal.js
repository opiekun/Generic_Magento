define([
	'jquery',
	'angular'
], function ($, angular) {

	var modal = function($rootScope, $uibModal, magezonBuilderUrl) {

		this.action = 'append';
		var self = this;
		self.modals = {};

		$rootScope.$on('closeAllModals', function(e) {
			self.closeModal();
		});

		this.registerModals = function(modals) {
			var newModals = {};
			angular.forEach(modals, function(modal, key) {
				modal['key'] = key;
				if (!modal.hasOwnProperty('key')) modal['size'] = {};
				if (!modal.hasOwnProperty('position')) modal['position'] = {};
				require([modal['element']], function (config) {
					self.modals[key] = angular.merge(modal, config);
				});
			});
		}

		this.getModal = function(name) {
			return self.modals[name];
		}

		this.getElement = function() {
			return this.element;
		}

		this.setElement = function(element) {
			this.element = element;
		}

		this.getAction = function() {
			return this.action;
		}

		this.setAction = function(action) {
			this.action = action;
		}

		this.getData = function() {
			return this.data;
		}

		this.setData = function(data) {
			this.data = data;
		}

		this.getOpenModal = function() {
			return this.openModal;
		}

		this.setOpenModal = function(openModal) {
			this.openModal = openModal;
		}

		this.open = function(name, config, closeCallback, cancelCallback) {
			if (!config) config = {};
			if (!closeCallback) closeCallback = function() {};
			if (!cancelCallback) cancelCallback = function() {};
			if (!config.backdrop) {
				self.closeModal();
			}
			var modal = self.getModal(name);
			if (modal) {
				if (modal.controller) {
					config.controller = modal.controller;
				} else {
					config.controller = 'modalBaseController';
				}
				if (!config.templateUrl && modal.templateUrl) config.templateUrl = modal.templateUrl;
				if (!config.controllerAs) config.controllerAs = 'mgz';
				config.windowTemplateUrl = magezonBuilderUrl.getViewFileUrl(config.windowTemplateUrl ? config.windowTemplateUrl : 'Magezon_Builder/js/templates/modal/window.html');
				if (config.openedClass) {
					config.openedClass += ' mgz-modal-open';
				} else {
					config.openedClass = 'mgz-modal-open';
				}
				if (config.windowClass) {
					config.windowClass += ' mgz-modal';
				} else {
					config.windowClass = 'mgz-modal';
				}
				config.windowClass += ' ' + self.getModalId(name);
				if (config.templateUrl) config.templateUrl = magezonBuilderUrl.getViewFileUrl(config.templateUrl);
				if (!config.hasOwnProperty('backdrop')) config['backdrop'] = false;
				if (!config.hasOwnProperty('keyboard')) config['keyboard'] = false;
				if (modal.resizable) config.windowClass += ' mgz-modal-resizable';
				var resolve = {
					modal: modal,
					form: {}
				};
				if (config.resolve) {
					config.resolve = angular.extend(resolve, config.resolve);
				} else {
					config.resolve = resolve;
				}
				var uibModal = $uibModal.open(config);
				uibModal.result.then(closeCallback, cancelCallback);
				if (!config.backdrop) {
					self.currentModal = uibModal;
					self.currentModal.name = name;
				}
				return uibModal;
			} else {
				alert('There is no modal with name ' + name);
			}
		}

		this.getModalId = function(name) {
			return 'mgz-modal-' + name;
		}

		this.closeModal = function(name) {
			if (!name && self.currentModal) {
				self.currentModal.dismiss('cancel');
			}
		}
	}

	return modal;
});