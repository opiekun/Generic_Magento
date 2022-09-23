/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Appointment
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */
require([
    'jquery'
], function ($) {

    /** show/hide slot container **/
    var appointmentSlots = setInterval(function () {
        if ($('input[name="product[same_slot_all_week_days]"]').length > 0)
        {
            if ($('input[name="product[same_slot_all_week_days]"]').val() == 1)
            {
                $('[data-index="add-appointment-slots-same-for-allweek"]').parent().show();
                $('[data-index="add-appointment-slots-for-weekdays"]').hide();
            } else {
                $('[data-index="add-appointment-slots-same-for-allweek"]').parent().hide();
                $('[data-index="add-appointment-slots-for-weekdays"]').show();
            }
            $('input[name="product[same_slot_all_week_days]"]').on('change', changeSlotContainer);
            clearInterval(appointmentSlots);
        }
    },500);

    function changeSlotContainer() {
        if ($('input[name="product[same_slot_all_week_days]"]').val() == 1)
        {
            $('[data-index="add-appointment-slots-same-for-allweek"]').parent().show();
            $('[data-index="add-appointment-slots-for-weekdays"]').hide();
        } else {
            $('[data-index="add-appointment-slots-same-for-allweek"]').parent().hide();
            $('[data-index="add-appointment-slots-for-weekdays"]').show();
        }
    }

    /** add time picker to start time fields **/
    var startTime = setInterval(function () {
        if ($("[data-index='start_time']").length > 0) {
            $(document).on("focus", "[data-index='start_time'] div input", function () {
                $(this).attr('readOnly',true);
                $(this).timepicker({
                    'timeFormat': 'hh:mm tt',
                    'ampm': true,
                    'disableTextInput': true

                });
            });
            clearInterval(StartTime);
        }
    },500);

    /** add time picker to end time fields **/
    var endTime = setInterval(function () {
        if ($("[data-index='end_time']").length > 0) {
            $(document).on("focus", "[data-index='end_time'] div input", function () {
                $(this).attr('readOnly',true);
                $(this).timepicker({
                    'timeFormat': 'hh:mm tt',
                    'ampm': true,
                    'disableTextInput': true

                });
            });
            clearInterval(endTime);
        }
    },500);
});
