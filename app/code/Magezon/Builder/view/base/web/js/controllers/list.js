define([
	'jquery',
	'angular'
], function($, angular) {

	var listCtrl = function(
		$scope,
		$rootScope,
		magezonBuilderService,
		elementManager,
		$sce,
		$controller,
		$timeout,
		magezonBuilderUrl
	) {

	var parent = $controller('baseController', {$scope: $scope});
	angular.extend(this, parent);

	var self = this;

	self.getElemName = function(type) {
        if (!$.isArray($scope.elementCache[type])) {
            $scope.elementCache[type] = [];
        }

        var elemName = type + '-' + $scope.getRandomInt();
        elemName = elemName.replace('bfb_', '');

        while (($.inArray(elemName, $scope.elementCache[type]) !== -1)) {
            elemName = type + '-' + $scope.getRandomInt();
        }
        $scope.elementCache[type].push(elemName);

        return elemName;
    }

	// ADD
	self.addElement = function(type, parent, openModal, data, index, replace) {
		var builderElement = self.getBuilderElement(type);
		var elem = elementManager.getNewElement(type);
		if (data) elem = angular.extend(elem, data);

		if (builderElement.children) {
			var childrenCount = builderElement.childrenCount ? builderElement.childrenCount : 1;
			for (var i = 0; i < childrenCount; i++) {
				var newElement = self.addElement(builderElement.children, elem, false);
				if (newElement && newElement.hasOwnProperty('title')) {
					newElement['title'] = newElement['title'] + ' ' + (i+1);
				}
			}
		}

		if (parent) {
			if (_.isNumber(index)) {
				if (replace) {
					parent.elements.splice(index, 1, elem);
				} else {
					parent.elements.splice(index, 0, elem);	
				}
			} else {
				parent.elements.push(elem);	
			}
		} else {
			if (_.isNumber(index)) {
				if (replace) {
					$scope.element.elements.splice(index, 1, elem);
				} else {
					$scope.element.elements.splice(index, 0, elem);	
				}
			} else {
				$scope.element.elements.push(elem);	
			}
		}

		if (openModal && (builderElement.hasOwnProperty('newOpenModal') && builderElement.newOpenModal || !builderElement.hasOwnProperty('newOpenModal'))) {
			self.editElement(elem);	
		}

		$scope.addAddBlock();

		return elem;
	}

	$scope.$on('addNewElment', function(e, item) {
		var type           = item.type;
		var mainElement    = $rootScope.builderConfig.mainElement;
		var builderElement = self.getBuilderElement(type);
		var openModal      = builderElement.hasOwnProperty('openModal') ? builderElement.openModal : true;
		if (!_.isNull(item.openModal) && !_.isUndefined(item.openModal)) openModal = item.openModal;
		var data = item.data ? item.data : {};
		var elem, historyType;
		if (item.elem) {
			if ((item.action == 'before' || item.action == 'after' || item.action == 'replace')) {
				var index = $scope.getElemIndex(item.elem);
				if (index !== -1) {
					switch(item.action) {
						case 'before':
							historyType = 'inserted_before';
							if (self.isProfile() && type != mainElement) {
								var row = self.addElement(mainElement, null, false, {}, index);
								elem = self.addElement(type, row['elements'][0], openModal, data);
							} else {
								elem = self.addElement(type, null, openModal, data, index);
							}
						break;

						case 'after':
							historyType = 'inserted_after';
							if (self.isProfile() && type != mainElement) {
								var row = self.addElement(mainElement, null, false, {}, index + 1);
								elem = self.addElement(type, row['elements'][0], openModal, data);
							} else {
								elem = self.addElement(type, null, openModal, data, index + 1);
							}
						break;

						case 'replace':
							historyType = 'replaced';
							if (self.isProfile() && type != mainElement) {
								var row = self.addElement(mainElement, null, false, {}, index, true);
								elem = self.addElement(type, row['elements'][0], openModal, data);
							} else {
								elem = self.addElement(type, null, openModal, data, index, true);
							}
						break;
					}
				}
			}

			if (item.action == 'append' && item.elem.id == $scope.element.id) {
				elem = self.addElement(type, null, openModal, data);
				historyType = 'added';
			}
		} else {
			if (self.isProfile()) {
				historyType = 'added';
				if (type === mainElement) {
					elem = self.addElement(type, null, openModal, data);	
				} else {
					var row = self.addElement(mainElement, null, false, {}, index, true);
					elem = self.addElement(type, row['elements'][0], openModal, data);
				}
			}
		}
		if (elem && historyType && (!data.hasOwnProperty('history') || (data.hasOwnProperty('history') && data.history))) {
			$rootScope.$broadcast('addHistory', {
				type: historyType,
				title: elem.builder.name
			});
			$rootScope.$broadcast('afterAddElement', elem);
		}
	});

	// REMOVE
	$scope.$on('removeElement', function(e, elem, data) {
		self.removeElement(elem, data);
	});

	self.removeElement = function(elem, data) {
		if ($scope.isChildren(elem)) {
			var index = $scope.getElemIndex(elem);
			$rootScope.$broadcast('beforeRemoveElement', elem);
			var _elem = $scope.element.elements.splice(index, 1)[0];
			$rootScope.$broadcast('removeParentElement', $scope.element);
			if (!data || (data && (data.hasOwnProperty('history') && data.history) || !data.hasOwnProperty('history'))) {
				$rootScope.$broadcast('addHistory', {
					type: 'removed',
					title: _elem.builder.name
				});
			}
			$rootScope.$broadcast('afterRemoveElement', _elem);
			$scope.addAddBlock();
		}
	}

	$scope.$on('removeParentElement', function(e, elem) {
		self.removeParentElement(elem);
	});

	self.removeParentElement = function(parent) {
		if ($scope.isChildren(parent)) {
			var index = $scope.getElemIndex(parent);
			if (parent.builder.children && !parent.elements.length) {
				$scope.element.elements.splice(index, 1);
			}
		}
	}

	// CLONE
	$scope.$on('cloneElement', function(e, elem) {
		self.cloneElement(elem);
	});

	self.cloneElement = function(elem) {
		if ($scope.isChildren(elem)) {
			var index   = $scope.getElemIndex(elem);
			var newElem = angular.copy(elem);
			elementManager.prepareElement(newElem, true);
			$scope.element.elements.splice(index + 1, 0, newElem);
			$rootScope.$broadcast('addHistory', {
				type: 'duplicated',
				title: newElem.builder.name
			});
			$rootScope.$broadcast('afterCloneElement', newElem);
		}
	}

	// CHANGE ELEMENT LAYOUT
	$scope.$on('changeElementLayout', function(e, data) {
		self.changeElementLayout(data);
	});

	self.changeElementLayout = function(data) {
		if ($scope.element.id === data.elem.id) {
			var types = data.type.split('_');
			for (var i = 0; i < types.length; i++) {
				var _type = types[i].split('');
				var elem  = $scope.element.elements[i];
				if (!elem) {
					elem = self.addElement($scope.element.builder.children, $scope.element);
				}
				var width;
				if (_type[1]==5) {
					width = 15
				} else {
					width = 12 * _type[0] / _type[1];
				}
				magezonBuilderService.setResponiveValue(elem, 'size', width);
			}
			$rootScope.$broadcast('addHistory', {
				type: 'changed',
				title: $scope.element.builder.name + ' Layout'
			});
		}
	}

	// Open Navigator Modal
	$scope.$on('openElementNavigator', function(e, elem) {
		if ($scope.isChildren(elem)) {
			$rootScope.activedElement = elem;
			elem.builder.actived = true;
			var parents = self.getParents();
			if ($scope.element.id) parents.push($scope.element);
			angular.forEach(parents, function(_elem) {
				_elem.builder.navigator.listVisible = true;
			});
			$rootScope.builderConfig.navigatorListVisible = true;
			$rootScope.$broadcast('openNavigatorModal');
		}
	});

	$scope.$on('editedElement', function(e, elem) {
		if ($scope.isChildren(elem)) {
			self.loadLiveElement(elem);
		}
	});

	// REMOVE
	var move = function (origin, destination) {
        var temp = $scope.element.elements[destination];
        $scope.element.elements[destination] = $scope.element.elements[origin];
        $scope.element.elements[origin] = temp;
    };

	$scope.$on('moveUpElement', function(e, elem) {
		self.moveUpElement(elem);
		$rootScope.$broadcast('resetElement');
	});

	self.moveUpElement = function(elem) {
		if ($scope.isChildren(elem)) {
			var index = $scope.getElemIndex(elem);
			if (index>0) {
				move(index, index - 1);
				$rootScope.$broadcast('addHistory', {
					type: 'move_up',
					title: elem.builder.name
				});
			}
		}
	}

	$scope.$on('moveDownElement', function(e, elem) {
		self.moveDownElement(elem);
	});

	self.moveDownElement = function(elem) {
		if ($scope.isChildren(elem)) {
			var index = $scope.getElemIndex(elem);
			if (index<$scope.element.elements.length-1) {
				move(index, index + 1);
				$rootScope.$broadcast('addHistory', {
					type: 'move_down',
					title: elem.builder.name
				});
			}
		}
	}

	$timeout(function() {
		$scope.addAddBlock();
	}, 100);

	return angular.copy(self);
};

return listCtrl;

});