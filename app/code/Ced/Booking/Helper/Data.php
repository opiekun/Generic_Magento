<?php
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

namespace Ced\Booking\Helper;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    const FONT_ICON_SIZE = '3x';
    const JS_DATE_FORMAT = 'yy-mm-dd';
    const PHP_DATE_FORMAT = 'Y-m-d';
    const ORDER_STATUS_PENDING = 'pending';
    const ORDER_STATUS_PROCESSING = 'processing';
    const ORDER_STATUS_COMPLETE = 'complete';
    const ORDER_STATUS_CLOSED = 'closed';
    const ORDER_STATUS_CANCELLED = 'cancelled';
    const ORDER_STATUS_PARTIALLY_INVOCED = 'partially_invoiced';
    const ORDER_STATUS_PARTIALLY_REFUNDED = 'partially_refunded';
    const XML_PATH_MAP_API_KEY = 'booking/booking_config/map_api_key';

    const MONDAY_CODE = 'mon';
    const TUESDAY_CODE = 'tue';
    const WEDNESDAY_CODE = 'wed';
    const THURSDAY_CODE = 'thu';
    const FRIDAY_CODE = 'fri';
    const SATURDAY_CODE = 'sat';
    const SUNDAY_CODE = 'sun';

    const APPOINTMENT_PRODUCT_TYPE = 'appointment';
    const EVENT_PRODUCT_TYPE = 'event';
    const RENTAL_PRODUCT_TYPE = 'rental';

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $dateTime
     * @param \Ced\Booking\Model\ResourceModel\Facilities\CollectionFactory $facilitiesCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $dateTime,
        \Ced\Booking\Model\ResourceModel\Facilities\CollectionFactory $facilitiesCollectionFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper
    ) {
        parent::__construct($context);
        $this->_moduleManager = $context->getModuleManager();
        $this->_storeManager = $storeManager;
        $this->_scopeConfigManager = $scopeConfigManager;
        $this->_localeDate = $dateTime;
        $this->_facilitiesCollectionFactory = $facilitiesCollectionFactory;
        $this->priceHelper = $priceHelper;
    }

    /**
     * @param $path
     * @param null $storeId
     * @return mixed
     */
    public function getStoreConfig($path, $storeId = null)
    {
        $store = $this->_storeManager->getStore($storeId);
        return $this->_scopeConfigManager->getValue($path, 'store', $store->getCode());
    }

    public function getOrderStatus()
    {
        return [self::ORDER_STATUS_PENDING, self::ORDER_STATUS_PROCESSING, self::ORDER_STATUS_PARTIALLY_REFUNDED, self::ORDER_STATUS_PARTIALLY_INVOCED];
    }

    /**
     * @param $price
     * @return float|string
     */
    public function getFormattedPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }

    /**
     * @return string
     */
    public function getCurrentDate()
    {
        return $this->_localeDate->date()->format('Y-m-d');
    }

    /**
     * @param $image
     * @return bool|string
     * @throws LocalizedException
     */
    public function getImageUrl($image)
    {
        $url = false;
        if ($image) {
            if (is_string($image)) {
                $url = $this->_storeManager->getStore()->getBaseUrl(
                    UrlInterface::URL_TYPE_MEDIA
                ) . 'booking/tmp/' . $image;
            } else {
                throw new LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }

    /**
     * @return false|string
     */
    public function getCurrentTime()
    {
        return $this->_localeDate->date()->format('H:i:s');
    }

    /** get all non working dates of product */
    public function getNonWorkingDates($product)
    {
        /** get all non working dates */
        $nonWorkingDates = $product->getNonWorkingDates();
        $unavailableDates = [];
        if ($nonWorkingDates) {
            $nonWorkingDatesArray = json_decode($nonWorkingDates, true);
            if (count($nonWorkingDatesArray) > 0) {
                foreach ($nonWorkingDatesArray as $nonworkingDate) {
                    $startDate = strtotime($nonworkingDate['start_date']);
                    $endDate = strtotime($nonworkingDate['end_date']);
                    for ($ndate = $startDate; $ndate <= $endDate; $ndate += 86400) {
                        $unavailableDates[] = date(Data::PHP_DATE_FORMAT, $ndate);
                    }
                }
            }
        }
        /** end of get all non working dates */
        return $unavailableDates;
    }

    public function getProductFacilities($_product)
    {
        $fIds = explode(',', $_product->getFacilityIds());
        if (count($fIds)) {
            $facilitiesCollection = $this->_facilitiesCollectionFactory->create();
            $facilitiesCollection->addFieldToFilter('id', ['in' => $fIds]);
            return $facilitiesCollection;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getEnabledBookingTypes()
    {
        if ($this->scopeConfig->getValue('booking/booking_products/type')) {
            return array_keys($this->scopeConfig->getValue('booking/booking_products/type'));
        } else {
            return [];
        }
    }

    public function getAllBookingTypes()
    {
        return [self::APPOINTMENT_PRODUCT_TYPE, self::EVENT_PRODUCT_TYPE,
            self::RENTAL_PRODUCT_TYPE];
    }

    /**
     * @return bool
     */
    public function isAnyBookingModuleEnabled()
    {
        if ($this->isModuleEnabled('Ced_Event') ||
            $this->isModuleEnabled('Ced_Appointment') ||
            $this->isModuleEnabled('Ced_Rental')) {
            return true;
        }
        return false;
    }

    /**
     * @param $moduleName
     * @return bool
     */
    public function isModuleEnabled($moduleName)
    {
        return $this->_moduleManager->isEnabled($moduleName);
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public function getAllDates($startDate, $endDate)
    {
        $dates = [];
        $d1 = strtotime($startDate);
        $d2 = strtotime($endDate);

        for ($i = $d1; $i <= $d2; $i += 86400) {
            $dates[] = date('Y-m-d', $i);
        }
        return $dates;
    }
}
