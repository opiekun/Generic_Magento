define(['jquery','owlcarousel' ,'domReady!'], function (jQuery) {


    (function ($,owlcarousel) {
        window.onload =function () {
                // Code that uses $'s $ can follow here.
                // services
                // ==============================
                var ap;
    
            if (jQuery("#autoPlay").val()==5000) {
                ap = 5000;
            } else {
                ap = false;
            }

                var owl = $("#owl-demo2");
                owl.owlCarousel(
                    {
                        items : 5, //10 items above 1000px browser width
                        itemsDesktop : [1000, 3], //5 items between 1000px and 901px
                        itemsDesktopSmall : [900, 3], // betweem 900px and 601px
                        itemsTablet : [600, 2], //2 items between 600 and 0
                        itemsMobile : [450, 1],
                        autoPlay : ap,
                        navigation: true,
                        navigationText  : ["<i class='icon-left-open'></i>","<i class='icon-right-open'></i>"],
                        itemsMobile : false // itemsMobile disabled - inherit from itemsTablet option
                      }
                );
        }
       
    }
    )});
// Code that uses other library's $ can follow here.

