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
 * @package     Ced_Appointment
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Appointment\Helper;

use Ced\Booking\Helper\Data as bookingHelper;
use Ced\Booking\Model\ResourceModel\RentOrders\CollectionFactory;
use Exception;
use Magento\Catalog\Model\ProductRepository;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Ced\Appointment\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MONDAY_NAME = 'Monday';

    const MONDAY_CODE = 'mon';

    const TUESDAY_NAME = 'Tuesday';

    const TUESDAY_CODE = 'tue';

    const WEDNESDAY_NAME = 'Wednesday';

    const WEDNESDAY_CODE = 'wed';

    const THURSDAY_NAME = 'Thursday';

    const THURSDAY_CODE = 'thu';

    const FRIDAY_NAME = 'Friday';

    const FRIDAY_CODE = 'fri';

    const SATURDAY_NAME = 'Saturday';

    const SATURDAY_CODE = 'sat';

    const SUNDAY_NAME = 'Sunday';

    const SUNDAY_CODE = 'sun';

    const SERVICE_ATTRIBUTE_CODE = 'service_type';

    const SERVICE_BOTH_OPTION = 'Both';

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param bookingHelper $bookingHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Eav\Model\Config $eavConfig,
        bookingHelper $bookingHelper,
        CollectionFactory $rentOrdersCollectionFactory,
        ProductRepository $productRepository
    ) {
        $this->eavConfig = $eavConfig;
        $this->bookingHelper = $bookingHelper;
        $this->rentOrdersCollectionFactory = $rentOrdersCollectionFactory;
        $this->productRepository = $productRepository;
        parent::__construct($context);
    }

    /**
     * @return int|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getServiceTypeBothOptionId()
    {
        $serviceType = 0;
        $attribute = $this->eavConfig->getAttribute('catalog_product', self::SERVICE_ATTRIBUTE_CODE);
        $options = $attribute->getSource()->getAllOptions();
        if (count($options) > 0) {
            foreach ($options as $option) {
                if ($option['label'] == self::SERVICE_BOTH_OPTION) {
                    $serviceType = $option['value'];
                }
            }
        }
        return $serviceType;
    }

    /**
     *  get appointment top link title
     */
    public function getAppointmentLink()
    {
        if ($this->bookingHelper->isModuleEnabled('Ced_Booking') && $this->scopeConfig->getValue('booking/appointment_config/enable', ScopeInterface::SCOPE_STORE)) {
            if ($this->scopeConfig->getValue('booking/appointment_config/appointment_link_enabled', ScopeInterface::SCOPE_STORE)) {
                return $this->scopeConfig->getValue('booking/appointment_config/appointment_link_title', ScopeInterface::SCOPE_STORE);
            }
        }
        return false;
    }

    /** get service type attribute options */
    public function getServiceTypeByOptionId($optionId)
    {
        $serviceType = '';
        $attribute = $this->eavConfig->getAttribute('catalog_product', self::SERVICE_ATTRIBUTE_CODE);
        $options = $attribute->getSource()->getAllOptions();
        if (count($options) > 0) {
            foreach ($options as $option) {
                if ($option['value'] == $optionId) {
                    $serviceType = $option['label'];
                }
            }
        }
        return $serviceType;
    }

    /**
     * @param $date
     * @param $productId
     * @return array
     * @throws Exception
     */
    public function getAvailableSlotsByDate($date, $productId)
    {
        $product = $this->productRepository->getById($productId);
        $status = [bookingHelper::ORDER_STATUS_PENDING, bookingHelper::ORDER_STATUS_COMPLETE, bookingHelper::ORDER_STATUS_PARTIALLY_INVOCED];

        /** @var  $unavailableDates */
        $unavailableDates = $this->bookingHelper->getNonWorkingDates($product);

        /** check whether the selected date is available or not*/
        if (in_array($date, $unavailableDates)) {
            return ['error' => true, 'message' => __('Shop is closed')];
        }
        /** end of check available date */

        /** get all available slots */
        $availableSlots = [];
        if ($product->getAppointmentSlots() != '') {
            $slots = json_decode($product->getAppointmentSlots(), true);
            $duration = $product->getDuration();
            $qtyPerSlot = $product->getQtyPerSlot();
            $currentDate = $this->bookingHelper->getcurrentdate();
            $currentTime = $this->bookingHelper->getcurrentTime();

            if ($product->getSameSlotAllWeekDays()) {
                /** same slot for all week days */
                if (count($slots) > 0) {
                    foreach ($slots as $slot) {
                        if ($slot['start_time'] != '' && $slot['end_time'] != '') {
                            $workStartTime = $slot['start_time'];
                            $workEndTime = $slot['end_time'];
                            $count = 0;
                            while (strtotime($workStartTime) < strtotime($workEndTime)) {
                                $count++;

                                /** first slot */
                                $startTime = date("H:i:s", strtotime($workStartTime));
                                $endTime = date("H:i:s", strtotime("+" . $duration . ' min', strtotime($startTime)));

                                $diff = (strtotime($workEndTime) - strtotime($workStartTime)) / 60;

                                if ($diff < $duration) {
                                    break;
                                }

                                $title = date("h:i a", strtotime($startTime)) . ' - ' . date("h:i a", strtotime($endTime));

                                /** @note check if the slot is booked for the selected date */
                                $startSelectedDate = $date . ' ' . date('H:i:s', strtotime(substr($title, 0, 8)));
                                $endSelectedDate = $date . ' ' . date('H:i:s', strtotime(substr($title, 10, 10)));

                                $rentOrders = $this->rentOrdersCollectionFactory->create();
                                $rentOrders->addFieldToFilter('product_id', $productId)
                                    ->addFieldToFilter('start_date', ['eq' => $startSelectedDate])
                                    ->addFieldToFilter('end_date', ['eq' => $endSelectedDate]);
                                $freeQty = 0;
                                $orderedQty = 0;
                                if (!empty($rentOrders)) {
                                    foreach ($rentOrders as $rentOrder) {
                                        if ($rentOrder->getStatus() == bookingHelper::ORDER_STATUS_CANCELLED) {
                                            $freeQty = $freeQty + $rentOrder->getQtyOrdered();
                                        } elseif ($rentOrder->getStatus() == bookingHelper::ORDER_STATUS_PARTIALLY_REFUNDED ||
                                            $rentOrder->getStatus() == bookingHelper::ORDER_STATUS_CLOSED) {
                                            $freeQty = $freeQty + $rentOrder->getQtyRefunded();
                                        } else {
                                            $orderedQty = $rentOrder->getQtyOrdered() + $orderedQty;
                                        }
                                    }
                                }

                                $bookedQty = $orderedQty - $freeQty;
                                if ($qtyPerSlot == $bookedQty) {
                                    $leftQty = 0;
                                } else {
                                    if ($bookedQty <= 0) {
                                        $leftQty = $qtyPerSlot;
                                    } else {
                                        $leftQty = $qtyPerSlot - $bookedQty;
                                    }
                                }

                                /** @note order qty code end */

                                if (strtotime($currentDate) == strtotime($date)) {
                                    if (strtotime($endTime) <= strtotime($workEndTime) && strtotime($workStartTime) >= strtotime($currentTime)) {
                                        $availableSlots[] = ['id' => $count, 'title' => $title, 'start' => $date, 'qty' => $leftQty];
                                    }
                                } else {
                                    if (strtotime($endTime) <= strtotime(date("H:i:s", strtotime($workEndTime)))) {
                                        $availableSlots[] = ['id' => $count, 'title' => $title, 'start' => $date, 'qty' => $leftQty];
                                    }
                                }
                                $workStartTime = date("H:i:s", strtotime("+" . $duration . ' min', strtotime($startTime)));
                            }
                        }
                    }
                }
            } else {
                /** different slots for each week day */
                $selectedDayName = strtolower(date('D', strtotime($date)));
                if (count($slots) > 0) {
                    foreach ($slots as $day => $option) {
                        if ($day == $selectedDayName) {
                            if ($option['status'] == 'open' && !empty($option['slots'])) {
                                foreach ($option['slots'] as $slot) {
                                    if ($slot['start_time'] != '' && $slot['end_time'] != '') {
                                        $workStartTime = $slot['start_time'];
                                        $workEndTime = $slot['end_time'];
                                        $count = 0;
                                        while (strtotime($workStartTime) < strtotime($workEndTime)) {
                                            $count++;

                                            /** first slot */
                                            $startTime = date("H:i:s", strtotime($workStartTime));
                                            $endTime = date("H:i:s", strtotime("+" . $duration . ' min', strtotime($startTime)));

                                            $diff = (strtotime($workEndTime) - strtotime($workStartTime)) / 60;

                                            if ($diff < $duration) {
                                                break;
                                            }

                                            $title = date("h:i a", strtotime($startTime)) . ' - ' . date("h:i a", strtotime($endTime));
                                            /** @note check if the slot is booked for the selected date */
                                            $startSelectedDate = $date . ' ' . date('H:i:', strtotime(substr($title, 0, 8)));
                                            $endSelectedDate = $date . ' ' . date('H:i:s', strtotime(substr($title, 10, 10)));

                                            $rentOrders = $this->rentOrdersCollectionFactory->create();
                                            $rentOrders->addFieldToFilter('product_id', $productId)
                                                ->addFieldToFilter('start_date', ['eq' => $startSelectedDate])
                                                ->addFieldToFilter('end_date', ['eq' => $endSelectedDate]);
                                            $freeQty = 0;
                                            $orderedQty = 0;
                                            if (!empty($rentOrders)) {
                                                foreach ($rentOrders as $rentOrder) {
                                                    if ($rentOrder->getStatus() == bookingHelper::ORDER_STATUS_CANCELLED) {
                                                        $freeQty = $freeQty + $rentOrder->getQtyOrdered();
                                                    } elseif ($rentOrder->getStatus() == bookingHelper::ORDER_STATUS_PARTIALLY_REFUNDED ||
                                                        $rentOrder->getStatus() == bookingHelper::ORDER_STATUS_CLOSED) {
                                                        $freeQty = $freeQty + $rentOrder->getQtyRefunded();
                                                    } else {
                                                        $orderedQty = $rentOrder->getQtyOrdered();
                                                    }
                                                }
                                            }

                                            $bookedQty = $orderedQty - $freeQty;
                                            if ($qtyPerSlot == $bookedQty) {
                                                $leftQty = 0;
                                            } else {
                                                if ($bookedQty <= 0) {
                                                    $leftQty = $qtyPerSlot;
                                                } else {
                                                    $leftQty = $qtyPerSlot - $bookedQty;
                                                }
                                            }
                                            /** @note order qty code end */

                                            if (strtotime($currentDate) == strtotime($date)) {
                                                if (strtotime($endTime) <= strtotime($workEndTime) && strtotime($workStartTime) >= strtotime($currentTime)) {
                                                    $availableSlots[] = ['id' => $count, 'title' => $title, 'start' => $date, 'qty' => $leftQty];
                                                }
                                            } else {
                                                if (strtotime($endTime) <= strtotime(date("H:i:s", strtotime($workEndTime)))) {
                                                    $availableSlots[] = ['id' => $count, 'title' => $title, 'start' => $date, 'qty' => $leftQty];
                                                }
                                            }
                                            $workStartTime = date("H:i:s", strtotime("+" . $duration . ' min', strtotime($startTime)));
                                        }
                                    }
                                }
                            } elseif ($option['status'] == 'closed') {
                                return ['error' => true, 'message' => __('Shop is closed')];
                            }
                        }
                    }
                }
            }
        }
        return $availableSlots;
    }

    /**
     * @param bool $quoteItems
     * @param $postData
     * @return float|int|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function checkAvailability($quoteItems = false, $postData)
    {
        $quoteqty = 0;
        $totalOrderedQty = 0;
        $orderStatus = $this->bookingHelper->getOrderStatus();
        if ($quoteItems) {
            foreach ($quoteItems as $item) {
                if ($item->getProductId() == $postData['product']) {
                    $productOptions = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                    $options = $productOptions['info_buyRequest'];
                    if (strtotime($options['appointment_selected_date']) == strtotime($postData['appointment_selected_date']) &&
                        $postData['appointment_selected_time'] == $options['appointment_selected_time']) {
                        $quoteqty += $options['qty'];
                    }
                }
            }
        }
        $time = explode('-', $postData['appointment_selected_time']);
        $startDate = date('Y-m-d H:i:s', strtotime($postData['appointment_selected_date'] . ' ' . $time[0]));
        $endDate = date('Y-m-d H:i:s', strtotime($postData['appointment_selected_date'] . ' ' . $time[1]));
        $productQty = $this->productRepository->getById($postData['product'])->getQtyPerSlot();
        $rentorderCollection = $this
            ->rentOrdersCollectionFactory
            ->create();
        $orderedQty = $rentorderCollection->addFieldToFilter('product_id', $postData['product'])
            ->addFieldToFilter('start_date', $startDate)
            ->addFieldToFilter('end_date', $endDate)
            ->addFieldToFilter('status', ['in' => $orderStatus])->getColumnValues('qty');
        if (!empty($orderedQty)) {
            $totalOrderedQty = array_sum($orderedQty);
        }
        $leftQty = $productQty - ($totalOrderedQty + $postData['qty'] + $quoteqty);
        return $leftQty;
    }
}
