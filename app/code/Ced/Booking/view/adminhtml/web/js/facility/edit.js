require(['jquery'],function ($) {

    var FacilityImageField = setInterval(function () {
        if ($('[name="image_type"]').length >0)
        {
            /** new case **/
            $('.file-uploader').parent().parent().hide();
            $('[data-index="icon"]').hide();

            $('[name="image_type"]').on('change',function () {
                if ($(this).val() =='image')
                {
                    $('.file-uploader').parent().parent().show();
                    $('[data-index="icon"]').hide();
                } else {
                    $('[data-index="icon"]').show();
                    $('.file-uploader').parent().parent().hide();
                }
            });


            /** edit case **/
            if ($('[name="image_type"]').val() =='image')
            {
                $('.file-uploader').parent().parent().show();
                $('[data-index="icon"]').hide();
            } else {
                $('[data-index="icon"]').show();
                $('.file-uploader').parent().parent().hide();
            }

            clearInterval(FacilityImageField);
        }
    },100);

});