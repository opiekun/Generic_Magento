require(
    ["jquery","cubeportfolio"],
    function ($,cubeportfolio) {

        (function ($, window, document, undefined) {
            var current_tab;
            var gridContainer = $('#grid-container'),
            filtersContainer = $('#filters-container');

            // init cubeportfolio
            gridContainer.cubeportfolio(
                {

                    defaultFilter: '*',

                    animationType: 'flipOut',

                    gapHorizontal: 45,

                    gapVertical: 30,

                    gridAdjustment: 'responsive',

                    caption: 'overlayBottomReveal',

                    displayType: 'lazyLoading',

                    displayTypeSpeed: 100,

                    // lightbox
                    lightboxDelegate: '.cbp-lightbox',
                    lightboxGallery: true,
                    lightboxTitleSrc: 'data-title',
                    lightboxShowCounter: true,

                    // singlePage popup
                    singlePageDelegate: '.cbp-singlePage',
                    singlePageDeeplinking: true,
                    singlePageStickyNavigation: true,
                    singlePageShowCounter: true,
                    singlePageCallback: function (url, element) {
                         alert("test");
                        // to update singlePage content use the following method: this.updateSinglePage(yourContent)
                        var t = this;

                        $.ajax(
                            {
                                url: url,
                                type: 'GET',
                                dataType: 'html',
                                timeout: 5000
                            }
                        )
                        .done(
                            function (result) {
                                t.updateSinglePage(result);
                            }
                        )
                        .fail(
                            function () {
                                t.updateSinglePage("Error! Please refresh the page!");
                            }
                        );

                    },

                    // single page inline
                    singlePageInlineDelegate: '.cbp-singlePageInline',
                    singlePageInlinePosition: 'above',
                    singlePageInlineShowCounter: true,
                    singlePageInlineInFocus: true,
                    singlePageInlineCallback: function (url, element) {
                        // to update singlePage Inline content use the following method: this.updateSinglePageInline(yourContent)
                    }
                }
            );

            // add listener for filters click
            filtersContainer.on(
                'click',
                '.cbp-filter-item',
                function (e) {

                    var me = $(this), wrap;

                    if (me.data('filter')!= "*") {
                        var element = document.getElementById('counter_'+me.data('filter'));
                        var itemscounter = element.textContent || element.innerText;
                        var totalitems = document.getElementById('total_counter'+me.data('filter')).value;
                        if (itemscounter==totalitems) {
                            $('.cbp-l-loadMore-button-link').hide();
                        } else {
                            $('.cbp-l-loadMore-button-link').show();
                        }
                    } else {
                        $('.cbp-l-loadMore-button-link').show();
                    }
                    current_tab = me.data('filter');
                    // get cubeportfolio data and check if is still animating (reposition) the items.
                    if (!$.data(gridContainer[0], 'cubeportfolio').isAnimating ) {
                        if (filtersContainer.hasClass('cbp-l-filters-dropdown') ) {
                            wrap = $('.cbp-l-filters-dropdownWrap');

                            wrap.find('.cbp-filter-item').removeClass('cbp-filter-item-active');

                            wrap.find('.cbp-l-filters-dropdownHeader').text(me.text());

                            me.addClass('cbp-filter-item-active');
                        } else {
                            me.addClass('cbp-filter-item-active').siblings().removeClass('cbp-filter-item-active');
                        }
                    }

                    // filter the items
                    gridContainer.cubeportfolio('filter', me.data('filter'), function () {});

                }
            );

            // activate counters
            gridContainer.cubeportfolio('showCounter', filtersContainer.find('.cbp-filter-item'));

            function changeId(id)
            {
                $(".cbp-l-loadMore-button-link [name=page]").attr(id);
            }

            // add listener for load more click
            $('.cbp-l-loadMore-button-link').on(
                'click',
                function (e) {

                    e.preventDefault();

                    var clicks, me = $(this), oMsg;
                    var c_tab = current_tab;
                    if (me.hasClass('cbp-l-loadMore-button-stop')) {
                        return;
                    }

                    // get the number of times the loadMore link has been clicked
                    clicks = $.data(this, 'numberOfClicks');
                    clicks = (clicks)? ++clicks : 1;
                    $.data(this, 'numberOfClicks', clicks);

                    // set loading status
                    oMsg = me.text();
                    me.text('LOADING...');

                    // perform ajax request
                    $.ajax(
                        {
                            url: me.attr('href'),
                            method: "POST",
                            dataType: "json"
                        }
                    )
                    .done(
                        function (res) {
                            var items, itemsNext;
                            var result = res.html;
                            // find current container
                            items = $(result).filter(
                                function () {

                
                
                                    return $(this).is('div' + '.cbp-loadMore-block' + clicks);
                                }
                            );
            
                            gridContainer.cubeportfolio(
                                'appendItems',
                                items.html(),
                                function () {
                                    // put the original message back
                                    me.text(oMsg);

                                    // check if we have more works
                                    itemsNext = $(result).filter(
                                        function () {
                                            return $(this).is('div' + '.cbp-loadMore-block' + (clicks + 1));
                                        }
                                    );

                                    if (itemsNext.length === 0) {
                                        me.text('NO MORE WORKS');
                                        me.addClass('cbp-l-loadMore-button-stop');
                                    }

                                }
                            );
                            $success =  $(result).filter("#next_pages").val()
                            $(".cbp-l-loadMore-button-link").attr("href", $success);
                            if (c_tab!= undefined) {
                                var element = document.getElementById('counter_'+c_tab);
                                var itemscounter = element.textContent || element.innerText;
                                var totalitems = document.getElementById('total_counter'+c_tab).value;
                                if (itemscounter==totalitems) {
                                    $('.cbp-l-loadMore-button-link').hide();
                                } else {
                                    $('.cbp-l-loadMore-button-link').show();
                                }
                            } else {
                                 $('.cbp-l-loadMore-button-link').show();
                            }
            

                        }
                    )
                    .fail(
                        function () {
           
                        }
                    );

                }
            );

    

        })(jQuery, window, document);
    }
);