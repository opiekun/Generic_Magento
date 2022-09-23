function styleFunctions($app, options, base_url) {
    btnStyle($app, options);
    arrowStyle($app, options);
    bgStyle($app, options, base_url);
    imageContainer($app, options);
}

function btnStyle($app, options) {
    let $btnElem = $app.find('.WS-lb-ctrl--close, .WS-lb-arrow--left, .WS-lb-arrow--right');
    if ( options.buttons.border_width > 3) { options.buttons.border_width = 0; }
    
    $btnElem.css({
        'background-color'  : options.buttons.color,
        'color'             : options.buttons.color_icon,
        'height'            : options.buttons.size+'px',
        'width'             : options.buttons.size+'px',
        'opacity'           : options.buttons.opacity
    });
    if ( options.buttons.border_width ) {
        $btnElem.css({
            'border-style'      : 'solid',
            'border-width'      : options.buttons.border_width+'px',
            'border-color'      : options.buttons.border_color,
        });
    }
        
    switch (options.buttons.style) {
        case 'square':
            $btnElem.css('border-radius', '0px');
            break;
        case 'rounded':
            $btnElem.css('border-radius', '5px');
            break;
        case 'circle':
            $btnElem.css('border-radius', '100%');
            break;
    }
    
}

function arrowStyle($app, options) {
    require(['jquery'], function($){
        let $arrows = $app.find('[class^="WS-lb-arrow-"]');
        switch (options.arrows.style) {
            case 'circle':
                $arrows.css('border-radius', '100%');
                break;
            case 'svelt' :
                $arrows.css('height', '150px')
                    .parent().css('bottom', `calc(50% + 75px)`);
                break;
            default:
                break;
        }
        $app.find('.WS-lb-arrow--left i').removeClass()
            .addClass(`${options.arrows.icon_style}-left`);
        $app.find('.WS-lb-arrow--right i').removeClass()
            .addClass(`${options.arrows.icon_style}-right`);

        if ( options.arrows.position === 'attached' ) {
            $app.find('.WS-ligtbox--container').append($('.WS-lb-arrows'));
            let $arr = ['right', 'left'];
            $.each($arr ,function(key, value) {
                $app.find(`.WS-lb-arrow--${value}`).css(value, `-${($app.find(`.WS-lb-arrow--${value}`).width()+10)}px`);
            });
        }
        if ( options.arrows.icon_size > $arrows.width() ) { options.arrows.icon_size = $arrows.width(); }
    
        $arrows.css({ 'font-size' : `${options.arrows.icon_size}px`,
                    'opacity'   : options.arrows.opacity })
            .addClass(`${options.arrows.hover_effect}-hover`);
        $arrows.hover(function() {
            $(this).css({ 'opacity' : options.arrows.hover_opacity });
        }, function() {
            $(this).css({
                'opacity' : options.arrows.opacity,
                
            });
        });
    });
}


function bgStyle($app, options, base_url) {
    $app.css('background-color', options.background.filter);
    if ( options.background.filter === 'image' ) {
        $app.append(`
            <span class="WS-lightbox--bg" style="opacity:${options.background.opacity};">
            </span>
        `);
    } else if ( typeof options.background.filter === 'string' || options.background.filter instanceof String) {
        $.getScript( `${base_url}/js/lib/colors.min.js` , function() {
            let $arr_color = $c.name2rgb(options.background.filter).a;
            $arr_color.push(options.background.opacity);
            $rgba = $arr_color.join(', ');
            $app.css('background-color', `rgba(${$rgba})`);
        });
    }
}

function imageContainer($app, options) {
    $app.find('.WS-lightbox--container .WS-lb-caption')
        .css({
            'border-bottom-left-radius' : options.image.border_radius,
            'border-bottom-right-radius' : options.image.border_radius
        });
    $app.find('.WS-lightbox--subcontainer')
        .css('background', options.image.bg)
    .find('img')
        .css({
            'border-radius' : options.image.border_radius,
            'border-color' : options.image.border_color,
            'border-width' : options.image.border_width
        });
}