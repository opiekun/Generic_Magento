($ => {
    $.fn.WS_lightbox_free = function (options) {
        createBaseDOM();
        const $app = $('.WS-lightbox');
        const $container = $('.WS-lightbox--container');
        let $_THIS = this;

        let $icon_styles = {
            default: 'fas fa-angle',
            angle: 'fas fa-angle',
            caret: 'fas fa-caret',
            chevron: 'fas fa-chevron'
        };
        options = $.extend({}, $.fn.WS_lightbox_free.options, options);
        return this.each(function () {
            // convert numeric
            switch (options.display_velocity) {
                case 'fast':
                    options.display_velocity = 200;
                    break;
                case 'normal':
                    options.display_velocity = 400;
                    break;
                case 'low':
                    options.display_velocity = 800;
                    break;
            }
            switch (options.slide_velocity) {
                case 'fast':
                    options.slide_velocity = 200;
                    break;
                case 'normal':
                    options.slide_velocity = 400;
                    break;
                case 'low':
                    options.slide_velocity = 800;
                    break;
            }
            switch (options.autoplay_velocity) {
                case 'fast':
                    options.autoplay_velocity = 1000;
                    break;
                case 'normal':
                    options.autoplay_velocity = 4000;
                    break;
                case 'low':
                    options.autoplay_velocity = 8000;
                    break;
            }
            ///////
            $.each($icon_styles, function (key, value) {
                if (options.arrows.icon_style === key) {
                    options.arrows.icon_style = value;
                }
            });
            // let base_url = window.location.origin;
            // let base_url = window.location.href;
            $.ajaxSetup({ 'cache': true });
            let base_url = 'https://cdn.jsdelivr.net/gh/alexandrebulete/ws-lisli/dist';

            $(this).find('li').each(function (index) {
                $(this).attr('data-item-order', index + 1);
                $(this).find('img').attr('data-item-order', index + 1);
            });

            createDOMElem();
            init();
            preloader();

            // basics mechanics
            //alert($('#custId').val());
          //c_stylizer
         // alert("asdasasdasd")
            $.getScript($('#custId').val(), function () {
               
                slideFunctions($app, options, $_THIS);
            });

            pos_Elements();

            // statusBoxPosition();

            // stylizer
           // alert("URL"+$('#c_stylizer').val());
            $.getScript($('#c_stylizer').val(), function () {
                styleFunctions($app, options, base_url);
            });
        });

        function init() {
            $app.find('.WS-lightbox--container img').css({
                'max-width': `calc(95vw - ${options.buttons.size * 2}px)`,
                'max-height': `calc(95vh - ${options.buttons.size * 2}px)`
            });
        }

        function preloader() {
            $app.ready(() => {
                $('.WS-lightbox-preloader').css('display', 'none');
            });
        }

        function pos_Elements() {
            if (options.arrows.position === 'inside') {
                $container.append($('.WS-lb-arrows'));
            }
            if (options.arrows.position === 'attached') {
                $container.append($('.WS-lb-arrows'));
                let $arr = ['right', 'left'];
                $.each($arr, function (key, value) {
                    $(`.WS-lb-arrows .WS-lb-arrow--${value}`).css(value, `-${$(`.WS-lb-arrow--${value}`).width() + 10}px`);
                });
            }
        }

        //////////////////////////////////////////////////////////////////
        //// CREATE ON DOM  /////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////
        function createButtonDOM($button) {
            let $iconElem = {
                close: 'far fa-times-circle'
            };
            $.each($iconElem, function (key, value) {
                if (key === $button) {
                    let $newElem = `<div class="WS-lb-ctrl--${$button}"><i class="${value}"></i></div>`;
                    $app.append($newElem);
                }
            });
            if ($button === 'arrows') {
                let $newElem = `<div class="WS-lb-${$button}">
                        <span class="WS-lb-arrow--left">
                            <i class="${options.arrows.icon_style}-left"></i>
                        </span> 
                        <span class="WS-lb-arrow--right">
                            <i class="${options.arrows.icon_style}-right"></i>
                        </span> 
                    </div>`;
                $app.append($newElem);
            }
        }

        function createDOMElem() {
            // options.arrows.position ??
            $.each(options, function (key, value) {
                if (key === 'arrows' && options.enable.includes('arrows')) {
                    createButtonDOM(key);
                }
            });
            if (options.enable.includes('close')) {
                createButtonDOM('close');
            }
        }

        function createBaseDOM() {
            let $base = `<div class="WS-lightbox" data-display="0" j-data="sdas">
                        <div class="WS-lightbox--container">
                            <div class="WS-lightbox--subcontainer">
                                <img 
                                    src="" 
                                    alt="alt" 
                                    title="title" 
                                    description="description">
                            </div>
                        </div>
                    </div>`;
            $('body').append($base);
        }
    };

    $.fn.WS_lightbox_free.options = {
        enable: ['arrows'],
        buttons: {
            size: 50,
            style: 'square',
            color: 'white',
            color_icon: 'black',
            border_width: 0,
            border_color: 'white',
            opacity: .8,
            hover_opacity: 1
        },

        arrows: {
            position: 'outside',
            style: 'square',
            icon_style: 'caret',
            icon_size: 24,
            opacity: .2,
            hover_effect: 'translate',
            hover_opacity: .5
        },

        background: {
            filter: 'image',
            opacity: .5
        },

        image: {
            border_radius: 0,
            border_color: '',
            border_width: 0,
            bg: 'none'
        },

        display_velocity: 'low',
        slide_velocity: 'normal'
    };
})(jQuery);