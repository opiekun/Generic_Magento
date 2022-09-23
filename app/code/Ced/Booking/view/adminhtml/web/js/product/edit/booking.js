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
 * @package     Ced_Booking
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */
require([
    'jquery'
], function ($) {

    /** add date picker to non working start date fields **/
    var startDate = setInterval(function () {
        if ($("[data-index='start_date']").length > 0) {
            $(document).on("focus", "[data-index='start_date'] div input", function () {
                $(this).datepicker();
            });
            clearInterval(startDate);
        }
    },500);

    /** add date picker to non working end date fields **/
    var endDate = setInterval(function () {
        if ($("[data-index='end_date']").length > 0) {
            $(document).on("focus", "[data-index='end_date'] div input", function () {
                $(this).datepicker();
            });
            clearInterval(endDate);
        }
    },500);

});