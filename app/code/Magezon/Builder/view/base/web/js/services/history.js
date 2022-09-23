define([
	'jquery',
	'underscore',
	'angular',
	'moment',
], function ($, _, angular, moment) {

	var history = function($rootScope, profileManager, magezonBuilderService) {
		var self = this;

		$rootScope.$on('exportShortcode', function(event) {
			if (!self.imported) {
				self.activeFirstItem();
				self.imported = true;
			}
		});

		$rootScope.$on('importShortcode', function(event) {
			if (!self.imported) {
				self.activeFirstItem();
				self.imported = true;
			}
		});

		this.activeFirstItem = function() {
			var item = this.addItem({
				type: 'editing_started',
				profile: profileManager.getShortCode()
			});
			item.selected = true;
		}

		$rootScope.$on('addHistory', function(event, data) {
			self.addItem(data);
			$rootScope.$broadcast('exportShortcode');
		});

		this.getTitle = function(name) {
			var title;
			angular.forEach($rootScope.builderConfig.historyLabels, function(_title, k) {
				if (k == name) {
					title = _title;
				}
			});
			return title;
		}

		this.addItem = function(data) {
			var action = self.getTitle(data.type);
			if (action) {
				if (data && data.hasOwnProperty('action')) action = data.action;
				self.clearItem();
				var profile = data.profile ? data.profile : profileManager.getShortCode();
				var strings = [];
				if (data.title) strings.push('<span class="mgz-builder-history-item__title">' + data.title + '</span>');
				if (data.subtitle) strings.push('<span class="mgz-builder-history-item__subtitle">' + data.subtitle + '</span>');
				if (action) strings.push('<span class="mgz-builder-history-item__action">' + action + '</span>');
				var title = strings.join(' ');
				var item = this.prepareItem(data.type, title, profile, false);
				$rootScope.history.push(item);
				self.activeItem(item);
				return item;
			}
		}

		this.prepareItem = function(type, title, profile, selected) {
			return {
				id: magezonBuilderService.uniqueid(),
				type: type,
				title: title,
				time: moment().valueOf(),
				profile: profileManager.getShortCode(),
				selected: selected
			};
		}

		this.activeItem = function(item) {
			angular.forEach($rootScope.history, function(_item, index) {
				if (item.id === _item.id) {
					_item.selected = true;
				} else {
					_item.selected = false;
				}
			});
			self.itemActived = item;
		}

		this.previewItem = function(item) {
			self.activeItem(item);
			var profile = profileManager.prepareProfile(item.profile);
			profileManager.updateElements(profile.elements);
			$rootScope.$broadcast('loadStyles');
		}

		this.clearItem = function() {
			var found = false;
			var history = angular.copy($rootScope.history);
			angular.forEach(history.reverse(), function(_item, index) {
				if (!found) {
					$rootScope.history.splice(history.length - index, 1);
				}
				if (_item.id === self.itemActived.id) {
					found = true;
				}
			});
		}

		this.applyItem = function(item) {
			self.previewItem(item);
			self.clearItem();
			$rootScope.$broadcast('exportShortcode');
		}
	}

	return history;
});