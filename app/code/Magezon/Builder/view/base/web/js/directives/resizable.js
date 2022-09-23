define([
    'jquery'
], function ($) {

	var resizableDir = function(magezonBuilderService, $rootScope) {
		return {
            restrict: 'AE',
            scope: {
            	rElement: '=',
                rDirections: '=',
                rCenteredX: '=',
                rCenteredY: '=',
                rWidth: '=',
                rHeight: '=',
                rFlex: '=',
                rGrabber: '@',
                rDisabled: '@'
            },
            link: function(scope, element, attr) {
                scope.sizes = [];
                magezonBuilderService.getBuilderConfig('resizableSizes', function(resizableSizes) {
                    scope.sizes = resizableSizes;
                });

            	scope.currentSize = scope.sizes[scope.sizes.length-1];

                var flexBasis = 'flexBasis' in document.documentElement.style ? 'flexBasis' :
                    'webkitFlexBasis' in document.documentElement.style ? 'webkitFlexBasis' :
                    'msFlexPreferredSize' in document.documentElement.style ? 'msFlexPreferredSize' : 'flexBasis';

                // register watchers on width and height attributes if they are set
                scope.$watch('rWidth', function(value){
                    element[0].style.width = scope.rWidth + 'px';
                });
                scope.$watch('rHeight', function(value){
                    element[0].style.height = scope.rHeight + 'px';
                });

                element.addClass('resizable');

                var style = window.getComputedStyle(element[0], null),
                    w,
                    h,
                    dir = scope.rDirections,
                    vx = scope.rCenteredX ? 2 : 1, // if centered double velocity
                    vy = scope.rCenteredY ? 2 : 1, // if centered double velocity
                    inner = scope.rGrabber ? scope.rGrabber : '<span></span>',
                    start,
                    dragDir,
                    axis,
                    info = {};

                var oldSize;

                var updateInfo = function(e) {
                    info.width = false; info.height = false;
                    if(axis === 'x')
                        info.width = parseInt(element[0].style[scope.rFlex ? flexBasis : 'width']);
                    else
                        info.height = parseInt(element[0].style[scope.rFlex ? flexBasis : 'height']);
                    info.id = element[0].id;
                    info.evt = e;

                    var size = scope.currentSize;
                    if (size && oldSize!=size) {
                        magezonBuilderService.setResponiveValue(scope.rElement, 'size', size['value']);
                        scope.$parent.$parent.$digest();
                    	oldSize = size;
                		element[0].style['width'] = '';
                		element.parent().children('.mgz-column-resize').html(size.shortLabel);
                        $rootScope.$broadcast('exportShortcode');
	                }
                };

                var getSize = function(percent) {

                	if (percent <= scope.sizes[0]['percent']) {
                		return scope.sizes[0];
                	}

                	if (percent >= scope.sizes[scope.sizes.length-1]['percent']) {
                		return scope.sizes[scope.sizes.length-1];
                	}

                    scope.sizes.sort(function(a,b){
                        return a['percent'] - b['percent'];
                    });

                    var newSizes = [];
                    var size, a, b;
                	for (var i = 0; i < scope.sizes.length; i++) {
                        var row = scope.sizes[i];

                        if (!size) {
                            size = scope.sizes[i];
                            continue;
                        }

                        a = Math.abs(percent - scope.sizes[i]['percent']);
                        b = Math.abs(percent - size['percent']);

                        if (a < b) {
                            size = scope.sizes[i];
                        }
                	}

                    if (size) {
                        return size;
                    }

                	return scope.currentSize;
                }

                var dragging = function(e) {
                    var _width       = $(element).width();
                    var _parentWidth = $(element).parents('.mgz-element-collection').eq(1).width() + 30;
                    if (isNaN(_width) || !_width) return;
                	if (_width > _parentWidth) {
						percent = 100;
                	} else {
						var prop, offset = axis === 'x' ? start - e.clientX : start - e.clientY;
						var percent      = (_width / _parentWidth) * 100;
	                }
					scope.currentSize = getSize(percent);
                    switch(dragDir) {
                        case 'right':
                            prop = scope.rFlex ? flexBasis : 'width';
                            element[0].style[prop] = (w - offset) + 'px';
                            break;
                        case 'left':
                            prop = scope.rFlex ? flexBasis : 'width';
                            element[0].style[prop] = w + (offset * vx) + 'px';
                            break;
                    }
                    updateInfo(e);
                };

                var dragEnd = function(e) {
                	element.parent().removeClass('mgz-resizing');
                	element[0].style['width'] = '';
                    updateInfo();
                    document.removeEventListener('mouseup', dragEnd, false);
                    document.removeEventListener('mousemove', dragging, false);
                    element.removeClass('no-transition');
                };
                var dragStart = function(e, direction) {
                    var width = element.closest('.mgz-element').outerWidth();
					element.parent().addClass('mgz-resizing');
                    element.width(width);
					dragDir = direction;
					axis    = dragDir === 'left' || dragDir === 'right' ? 'x' : 'y';
					start   = axis === 'x' ? e.clientX : e.clientY;
                    w = parseInt(style.getPropertyValue('width'));
                    h = parseInt(style.getPropertyValue('height'));

                    //prevent transition while dragging
                    element.addClass('no-transition');

                    document.addEventListener('mouseup', dragEnd, false);
                    document.addEventListener('mousemove', dragging, false);

                    // Disable highlighting while dragging
                    if(e.stopPropagation) e.stopPropagation();
                    if(e.preventDefault) e.preventDefault();
                    e.cancelBubble = true;
                    e.returnValue = false;
                    updateInfo(e);
                };

                dir.forEach(function (direction) {
                    var grabber = document.createElement('div');

                    // add class for styling purposes
                    grabber.setAttribute('class', 'mgz-resize');
                    grabber.innerHTML = inner;
                    element[0].appendChild(grabber);
                    grabber.ondragstart = function() { return false; };
                    grabber.addEventListener('mousedown', function(e) {
                        var disabled = (scope.rDisabled === 'true');
                        if (!disabled && e.which === 1) {
                            // left mouse click
                            dragStart(e, direction);
                        }
                    }, false);

                    if(!element.parent().children('.mgz-column-resize').length) {
                    	element.parent().append('<div class="mgz-column-resize"></div>');
                    }
                });
            }
        };
	};

	return resizableDir;
});