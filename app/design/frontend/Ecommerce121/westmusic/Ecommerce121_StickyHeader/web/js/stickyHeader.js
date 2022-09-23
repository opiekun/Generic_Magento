define(["jquery", "mage/menu", "jquery-ui-modules/widget"], function ($, menu) {
    "use strict";
    $.widget("mage.sticky", {
        options: {
            /**
             * Element selector, who's height will be used to restrict the
             * maximum offsetTop position of the stuck element.
             * Default uses document body.
             * @type {String}
             */
            container: "",

            /**
             * Spacing in pixels above the stuck element
             * @type {Number|Function} Number or Function that will return a Number
             */
            spacingTop: 0,

            /**
             * Allows postponing sticking, until element will go out of the
             * screen for the number of pixels.
             * @type {Number|Function} Number or Function that will return a Number
             */
            stickAfter: 0,

            /**
             * CSS class for active sticky state
             * @type {String}
             */
            stickyClass: "_sticky",

            /**
             * Name of the widget
             * @type {String}
             */
            name: "",

            /**
             * Class where the widget is applied
             */
            cssAppliedClass: "",
        },

        heightButton: $(".box-tocart")
            ?   (typeof $(".product-info-price").offset() !== 'undefined') ? $(".product-info-price").offset().top : 0 +
                (typeof $(".product-info-price").height() !== 'undefined') ? $(".product-info-price").height() : 0
            :   (typeof $(".product-info-price").offset() !== 'undefined' ) ? $(".product-info-price").offset().top : 0 +
                (typeof $(".product-info-price").height() !== 'undefined') ? $(".product-info-price").height() : 0 +
                $(".box-tocart").height(),
        heightProductInfo: 0,
        mobileScreenWidth: 964,
        headerHeight: 0,
        /**
         * Retrieve option value
         * @param  {String} option
         * @return {*}
         * @private
         */
        _getOptionValue: function (option) {
            var value = this.options[option] || 0;

            if (typeof value === "function") {
                value = this.options[option]();
            }

            return value;
        },

        /**
         * Bind handlers to scroll event
         * @private
         */
        _create: function () {
            $(window).on({
                scroll: $.proxy(this._stick, this),
                resize: $.proxy(this.reset, this),
            });

            this.element.on("dimensionsChanged", $.proxy(this.reset, this));

            this.reset();
        },

        /**
         * float Block on windowScroll
         * @private
         */
        _stick: function () {
            var offset,
                isStatic,
                stuck,
                stickAfter,
                headerHeight = $(".page-header").height(),
                windowScroll = $(window).scrollTop(),
                self = this;
            isStatic = this.element.css("position") === "static";

            if (!isStatic && this.element.is(":visible")) {
                offset =
                    $(document).scrollTop() -
                    this.parentOffset +
                    this._getOptionValue("spacingTop");

                offset = Math.max(0, Math.min(offset, this.maxOffset));

                stuck = this.element.hasClass(this.options.stickyClass);
                stickAfter = this._getOptionValue("stickAfter");
                if (offset && !stuck && offset < stickAfter) {
                    offset = 0;
                }

                // Sticky header for product detail page.
                if (
                    $("body.catalog-product-view").length > 0 &&
                    this.options.name == "pdp"
                ) {
                    var mediaBlockHeight = 0;
                    var productInfoMediaHeight = $('.product-info-media-block').outerHeight();
                    $('.product.media').css('min-height', 0);
                    if (window.screen.width > this.mobileScreenWidth) {
                        mediaBlockHeight = $('.product.media').height();
                        self.heightProductInfo = 0;
                        $('#maincontent').css( "padding-top",0);
                        if (self.heightProductInfo <= 0) {
                            this.element
                                .toggleClass(self.options.stickyClass)
                                .removeClass("sticky-header-pdp");
                            self.heightProductInfo = self.element.height();
                        }
                        if (windowScroll - self.heightProductInfo - headerHeight > -30) {
                            self.element
                                .toggleClass(self.options.stickyClass)
                                .addClass("sticky-header-pdp");
                            if (self.heightProductInfo > mediaBlockHeight) {
                                $('.product.media').css('min-height', self.heightProductInfo);
                            }

                        } else {
                            $('#maincontent').css( "padding-top",0);
                            this.element
                                .toggleClass(self.options.stickyClass)
                                .removeClass("sticky-header-pdp");
                        }
                    } else {
                        if (self.heightProductInfo <= 0) {
                            this.element
                                .toggleClass(self.options.stickyClass)
                                .removeClass("sticky-header-pdp");
                            self.heightProductInfo = self.element.height();
                        }
                        if (mediaBlockHeight <= 0) {
                            mediaBlockHeight = $('.product.media').height();
                        }
                        if (windowScroll - productInfoMediaHeight > -30) {
                            console.log('windowScroll ', windowScroll);
                            console.log('productInfoMediaHeight ', productInfoMediaHeight);
                            if (self.heightProductInfo <= 0) {
                                this.element
                                    .toggleClass(self.options.stickyClass)
                                    .removeClass("sticky-header-pdp");
                                self.heightProductInfo = self.element.height();
                            }
                            // $('#maincontent').css( "padding-top", self.heightProductInfo);

                            $('.product.media').css('min-height', mediaBlockHeight + self.heightProductInfo);
                            self.element
                                .toggleClass(self.options.stickyClass)
                                .addClass("sticky-header-pdp");

                        } else {
                            $('#maincontent').css( "padding-top",0);
                            this.element
                                .toggleClass(self.options.stickyClass)
                                .removeClass("sticky-header-pdp");
                        }
                    }
                }

                // Default sticky header
                if ($(".product-info-main").hasClass("hasSticky") == false) {
                    if (windowScroll > this.options.stickAfter) {
                        $("#sticky-header-btn-navigation").show();
                        $(".sticky-header-navigation").hide();
                        switch (this.options.name) {
                            case "header":
                                this.element
                                    .toggleClass(
                                        this.options.stickyClass,
                                        offset > 0
                                    )
                                    .addClass("sticky-header-header");
                                $(".page-wrapper").css(
                                    "padding-top",
                                    this.options.paddingTopPage
                                );
                                break;
                            case "navigation":
                                this.element
                                    .toggleClass(
                                        this.options.stickyClass,
                                        offset > 0
                                    )
                                    .addClass("sticky-header-navigation");
                                var topValue =
                                    this.options.stickAfter < headerHeight
                                        ? this.options.stickAfter
                                        : headerHeight;
                                $(this.options.cssAppliedClass).css(
                                    "top",
                                    topValue
                                );
                                break;
                        }
                    } else {
                        $(".sticky-header-navigation").show();
                        $("#sticky-header-btn-navigation").hide();
                        $(".page-wrapper").css("padding-top", 0);
                        switch (this.options.name) {
                            case "header":
                                this.element
                                    .toggleClass(
                                        this.options.stickyClass,
                                        offset > 0
                                    )
                                    .removeClass("sticky-header-header");
                                break;
                            case "navigation":
                                $(this.options.cssAppliedClass).css("top", 0);
                                this.element
                                    .toggleClass(
                                        this.options.stickyClass,
                                        offset > 0
                                    )
                                    .removeClass("sticky-header-navigation");
                                break;
                        }
                    }
                }
            }
        },

        /**
         * Defines maximum offset value of the element.
         * @private
         */
        _calculateDimens: function () {
            var $parent = this.element.parent(),
                topMargin = parseInt(this.element.css("margin-top"), 10),
                parentHeight = $parent.height() - topMargin,
                height = this.element.innerHeight(),
                maxScroll = document.body.offsetHeight - window.innerHeight;

            if (this.options.container.length > 0) {
                maxScroll = $(this.options.container).height();
            }

            this.parentOffset = $parent.offset().top + topMargin;
            this.maxOffset = maxScroll - this.parentOffset;
            if (this.maxOffset + height >= parentHeight) {
                this.maxOffset = parentHeight - height;
            }

            return this;
        },
        /**
         * Facade method that palces sticky element where it should be.
         */
        reset: function () {
            this.heightButton = $(".box-tocart")
                ?   (typeof $(".product-info-price").offset() !== 'undefined') ? $(".product-info-price").offset().top : 0 +
                    (typeof $(".product-info-price").height() !== 'undefined') ? $(".product-info-price").height() : 0
                :   (typeof $(".product-info-price").offset() !== 'undefined' ) ? $(".product-info-price").offset().top : 0 +
                    (typeof $(".product-info-price").height() !== 'undefined') ? $(".product-info-price").height() : 0 +
                    $(".box-tocart").height();
            this._calculateDimens()._stick();
        },
    });

    // Show hide Navigation Bar
    $(document).on("click", "#sticky-header-btn-navigation", function () {
        $(".sticky-header-navigation").toggle();
    });
    return $.mage.sticky;
});
