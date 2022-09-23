define([
	'jquery'
	], function ($) {

		// https://embed.plnkr.co/plunk/amkxZc
		var directive = function($compile, $timeout) {
			var template =  '<div class="mgz-tooltip tooltip {{placement}}" ng-class="{ in: isOpen, fade: animation() }"><div class="tooltip-arrow"></div><div class="tooltip-inner" ng-bind="tooltipTitle"></div></div>';

			return {
				scope: { tooltipTitle: '@mgzTooltip', placement: '@tooltipPlacement', animation: '&tooltipAnimation' },
				link: function ( scope, element, attr ) {
					var tooltip = $compile( template )( scope ), 
					transitionTimeout;
					var hoverd;

      				// Calculate the current position and size of the directive element.
      				function getPosition() {
      					return {
      						width: element.prop( 'offsetWidth' ),
      						height: element.prop( 'offsetHeight' ),
      						top: element.prop( 'offsetTop' ),
      						left: element.prop( 'offsetLeft' )
      					};
      				}

      				// Show the tooltip popup element.
      				function show() {
      					var position,
      					ttWidth,
      					ttHeight,
      					ttPosition;

        				// If no placement was provided, default to 'top'.
        				scope.placement = scope.placement || 'top';

				        // If there is a pending remove transition, we must cancel it, lest the
				        // toolip be mysteriously removed.
				        if ( transitionTimeout ) $timeout.cancel( transitionTimeout );

				        // Lazy compile the tooltip element
				        // FIXME: For some reason, this does *not* always work correctly on the 
				        // *first* run, but does so on all subsequent runs.
				        //tooltip = tooltip ||  $compile( template )( scope );

        				// Set the initial positioning.
        				tooltip.css({ top: 0, left: 0, display: 'block' });

				        // Now we add it to the DOM because need some info about it. But it's not 
				        // visible yet anyway.
				        element.after( tooltip );

				        // Get the position of the directive element.
				        position = getPosition();
				        
				        // Get the height and width of the tooltip so we can center it.
				        ttWidth = tooltip.prop( 'offsetWidth' );
				        ttHeight = tooltip.prop( 'offsetHeight' );

				        // Calculate the tooltip's top and left coordinates to center it with
				        // this directive.
				        switch ( scope.placement ) {
				        	case 'right':
				        	ttPosition = {
				        		top: (position.top + position.height / 2 - ttHeight / 2) + 'px',
				        		left: (position.left + position.width) + 'px'
				        	};
				        	break;
				        	case 'bottom':
				        	ttPosition = {
				        		top: (position.top + position.height) + 'px',
				        		left: (position.left + position.width / 2 - ttWidth / 2) + 'px'
				        	};
				        	break;
				        	case 'left':
				        	ttPosition = {
				        		top: (position.top + position.height / 2 - ttHeight / 2) + 'px',
				        		left: (position.left - ttWidth) + 'px'
				        	};
				        	break;
				        	default:
				        	ttPosition = {
				        		top: (position.top - ttHeight) + 'px',
				        		left: (position.left + position.width / 2 - ttWidth / 2) + 'px'
				        	};
				        	break;
				        }

				        // Now set the calculated positioning.
				        tooltip.css( ttPosition );

				        if (scope.placement == 'top' || scope.placement == 'bottom') {
				        	var arrowSel = tooltip.find('.tooltip-arrow');
				        	arrowSel.css('left', (tooltip.width() - arrowSel.outerWidth(true)) / 2);
				    	}

				        // And show the tooltip.
				        scope.isOpen = true;
				    }

      				// Hide the tooltip popup element.
      				function hide() {
				        transitionTimeout = $timeout( function () {
				        	if (!hoverd) {
				        		scope.isOpen = false;
				        		//tooltip.remove();
				        	}
				        }, 500);
				    }

				    
				    tooltip.hover(function() {
				    	scope.$apply( show );
				    	hoverd = true;
				    }, function() {
				    	hoverd = false;
				    	scope.$apply( hide );
				    })

			      	// Register the event listeners.
			      	element.bind( 'mouseenter', function() {
			      		$('.mgz-tooltip').remove();
			      		scope.$apply( show );
			      		hoverd = true;
			      	});
			      	element.bind( 'mouseleave', function() {
			      		hoverd = false;
			      		scope.$apply( hide );
			      	});
			      }
			  };
			}

			return directive;
		});