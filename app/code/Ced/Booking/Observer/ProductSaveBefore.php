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
namespace Ced\Booking\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ObjectManager;
use Ced\Booking\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
/**
 * Class ProductSaveBefore
 * @package Ced\Booking\Observer
 */
class ProductSaveBefore implements ObserverInterface
{
    /**
     * ProductSaveBefore constructor.
     * @param RequestInterface $request
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    function __construct(
        RequestInterface $request,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        Data $bookingHelper,
        \Ced\Booking\Model\ResourceModel\RentOrders\CollectionFactory $orderCollectionFactory
    ) {

        $this->request = $request;
        $this->jsonHelper = $jsonHelper;
        $this->_bookingHelper = $bookingHelper;
        $this->orderCollection = $orderCollectionFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
       
        $productdata = $observer->getEvent()->getProduct();
        $postData = $this->request->getPostValue();
        $allBookingTypes = $this->_bookingHelper->getAllBookingTypes();
        if (!empty($postData) && in_array($productdata->getTypeId(),$allBookingTypes))
        {
            $productdata->setStockData([
                'use_config_manage_stock' => 0,
                'is_in_stock' => 1,
                'manage_stock' => 0,
                'use_config_notify_stock_qty' => 0
            ]);

            if ($productdata->getTypeId() == 'appointment' && $this->_bookingHelper->isModuleEnabled('Ced_Appointment')) {
                $daySlots = [];
                if (isset($postData['product']['same_slot_all_week_days']) && $postData['product']['same_slot_all_week_days'] == 0) {
                    $daysArray = [Data::MONDAY_CODE, Data::TUESDAY_CODE, Data::WEDNESDAY_CODE, Data::THURSDAY_CODE, Data::FRIDAY_CODE, Data::SATURDAY_CODE, Data::SUNDAY_CODE];
                    foreach ($daysArray as $day) {
                        if (isset($postData['product'][$day . '_slots'])) {
                            $slotArray = $postData['product'][$day . '_slots'];
                        } else {
                            $slotArray = '';
                        }
                        $daySlots[$day]['status'] = $postData['product'][$day . 'status'];
                        $daySlots[$day]['slots'] = $slotArray;
                    }
                } elseif (isset($postData['product']['add-appointment-slots-same-for-allweek'])) {
                    $daySlots = $postData['product']['add-appointment-slots-same-for-allweek'];
                }
                $encodedData = $this->jsonHelper->jsonEncode($daySlots);
                $productdata->setData('appointment_slots', $encodedData);
            }

            /** set non working rules */
            if (isset($postData['product']['non_working_dates']) && count($postData['product']['non_working_dates']) > 0)
            {
                $encodedData = $this->jsonHelper->jsonEncode($postData['product']['non_working_dates']);
                $productdata->setData('non_working_dates',$encodedData);
            }

            if (isset($postData['links']['facilities']))
            {
                $facilityIds = array_column($postData['links']['facilities'],'id');
                $imploadedfIds = implode($facilityIds,',');
                $productdata->setData('facility_ids',$imploadedfIds);
            }
            if ($productdata->getTypeId() == 'event' && $this->_bookingHelper->isModuleEnabled('Ced_Event')) {
                $eventDate = false;
                $eventTicket = false;
                if ($productdata->getId() != '') {
                    if (!$productdata->getCustomerChooseDate()) {
                        $orderCollection = $this->orderCollection->create();
                        $orderCollection->addFieldToFilter('product_id', $productdata->getId())
                            ->addFieldToFilter('type', 'fixed');
                        if ($orderCollection->count()) {
                            throw new LocalizedException(__('Order placed for this product , so you can\'t change the event dates/tickets.'));
                        }
                    }
                }
                if (isset($postData['product']['event_dates']) && !empty($postData['product']['event_dates'])) {
                    $eventDates = $postData['product']['event_dates'];
                    $eventDatesEncoded = $this->jsonHelper->jsonEncode($eventDates);
                    $productdata->setData('event_dates', $eventDatesEncoded);
                    $eventDate = true;
                }
                if (isset($postData['product']['event_tickets']) && !empty($postData['product']['event_tickets'])) {
                    $eventTickets = $postData['product']['event_tickets'];
                    $eventTicketsEncoded = $this->jsonHelper->jsonEncode($eventTickets);
                    $productdata->setData('event_tickets', $eventTicketsEncoded);
                    $eventTicket = true;
                }
                if (!$eventDate && !$eventTicket)
                {
                    throw new LocalizedException(__('Please create event date & ticket.'));
                } elseif (!$eventDate) {
                    throw new LocalizedException(__('Please add event date.'));
                } elseif (!$eventTicket) {
                    throw new LocalizedException(__('Please create event ticket.'));
                }
            }
        }
        return $this;
    }
}



