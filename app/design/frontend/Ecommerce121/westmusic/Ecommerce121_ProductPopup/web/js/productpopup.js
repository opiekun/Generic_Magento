require(["jquery"], function ($) {
    $(document).ready(function () {
        $(document).click(function () {
            if($('.page-title-wrapper.product .info-popup').is(":visible")
                && !$('.amcform-popup-block').is(":visible")) {
                $('.page-title-wrapper.product .info-popup').hide('fast');
            }
        });
        $('.page-title-wrapper.product .page-title').click(function (event) {
            $('.page-title-wrapper.product .info-popup').show('fast');
            event.stopPropagation();
        });
    });
});
