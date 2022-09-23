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

use Ced\Booking\Helper\Data;
use Ced\Booking\Model\RentOrdersFactory;
use Ced\Booking\Model\ResourceModel\RentOrders;
use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class PlaceOrderAfter
 * @package Ced\Booking\Observer
 */
class PlaceOrderAfter implements ObserverInterface
{
    /**
     * @var RentOrdersFactory
     */
    protected $_rentOrdersModelFactory;

    /**
     * @var Data
     */
    protected $_bookingHelper;

    /**
     * @var RentOrders
     */
    protected $rentOrdersResourceModel;

    /**
     * PlaceOrderAfter constructor.
     * @param RentOrdersFactory $rentOrdersModelFactory
     * @param RentOrders $rentOrdersResourceModel
     * @param Data $bookingHelper
     */
    public function __construct(
        RentOrdersFactory $rentOrdersModelFactory,
        RentOrders $rentOrdersResourceModel,
        Data $bookingHelper
    ) {
        $this->_rentOrdersModelFactory = $rentOrdersModelFactory;
        $this->_bookingHelper = $bookingHelper;
        $this->rentOrdersResourceModel = $rentOrdersResourceModel;
    }

    /**
     * @param Observer $observer
     * @throws LocalizedException
     * @throws AlreadyExistsException
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        foreach ($order->getAllItems() as $items) {
            $productOptions = $items->getProductOptions();

            /** if the product type is appointment */
            if ($items->getProductType() == Data::APPOINTMENT_PRODUCT_TYPE) {
                if (isset($productOptions['info_buyRequest'])) {
                    $appointmentData = $productOptions['info_buyRequest'];
                    try {
                        $appointmentDate = $appointmentData['appointment_selected_date'];
                        $appointmentTime = $appointmentData['appointment_selected_time'];

                        /** convert appointment from-to time to array */
                        $appointmentTimeArray = explode('-', $appointmentTime);
                        $startTime = str_replace(' ', '', $appointmentTimeArray[0]);
                        $endTime = str_replace(' ', '', $appointmentTimeArray[1]);

                        /** appointment date + time */
                        $startDateTime = $appointmentDate . ' ' . date('H:i:s', strtotime($startTime));
                        $endDateTime = $appointmentDate . ' ' . date('H:i:s', strtotime($endTime));

                        $rentOrderModel = $this->_rentOrdersModelFactory->create();
                        $rentOrderModel->setData('order_id', $order->getIncrementId())
                            ->setData('product_id', $items->getProductId())
                            ->setData('start_date', $startDateTime)
                            ->setData('end_date', $endDateTime)
                            ->setData('qty_ordered', $items->getQtyOrdered())
                            ->setData('product_type', $items->getProductType())
                            ->setData('status', Data::ORDER_STATUS_PENDING);
                        $this->rentOrdersResourceModel->save($rentOrderModel);
                    } catch (\Exception $e) {
                        throw new LocalizedException(__($e->getMessage()));
                    }
                }
            } elseif ($items->getProductType() == Data::RENTAL_PRODUCT_TYPE) {
                if (isset($productOptions['info_buyRequest'])) {
                    $data = $productOptions['info_buyRequest'];
                    $rentalStartDate = '';
                    $rentalEndDate = '';
                    $totalValue = 0;
                    try {
                        if ($data['rental_type'] == 'daily') {
                            $rentalStartDate = $data['start_date'];
                            if (isset($data['number_of_days'])) {
                                $rentalEndDate = $data['number_of_days'] == 1 ? $data['start_date'] : date('Y-m-d', strtotime('+ ' . ($data['number_of_days'] - 1) . ' days', strtotime($data['start_date'])));
                            } else {
                                $rentalEndDate = $data['end_date'];
                            }
                            $totalValue = count($this->_bookingHelper->getAllDates($rentalStartDate, $rentalEndDate));
                        } elseif ($data['rental_type'] == 'hourly') {
                            $rentalStartDate = $data['start_date'];
                            $rentalEndDate = $data['start_date'];
                            $rentalStartTime = date('H:i:s', strtotime($data['start_time']));
                            $rentalEndTime = date('H:i:s', strtotime($data['end_time']));
                            $totalValue = $data['total_value'];
                        }
                        $rentOrderModel = $this->_rentOrdersModelFactory->create();
                        $rentOrderModel->setData('order_id', $order->getIncrementId())
                            ->setData('product_id', $items->getProductId())
                            ->setData('start_date', $rentalStartDate)
                            ->setData('end_date', $rentalEndDate)
                            ->setData('qty_ordered', $items->getQtyOrdered())
                            ->setData('product_type', $data['rental_type'])
                            ->setData('status', Data::ORDER_STATUS_PENDING);

                        if ($data['rental_type'] == 'daily') {
                            $rentOrderModel->setData('total_days', $totalValue);
                        } elseif ($data['rental_type'] == 'hourly') {
                            $rentOrderModel->setData('total_hours', $totalValue);
                            $rentOrderModel->setData('start_time', $rentalStartTime);
                            $rentOrderModel->setData('end_time', $rentalEndTime);
                        }

                        $this->rentOrdersResourceModel->save($rentOrderModel);
                    } catch (\Exception $e) {
                        throw new LocalizedException(__($e->getMessage()));
                    }
                }
            } elseif ($items->getProductType() == Data::EVENT_PRODUCT_TYPE && $this->_bookingHelper->isModuleEnabled('Ced_Event')) {
                $eventData = $productOptions['info_buyRequest'];
                if (isset($eventData['event_ticket']) && !empty($eventData['event_ticket'])) {
                    foreach ($eventData['event_ticket'] as $ticket) {
                        /** @var  $eventOrder */
                        $rentOrderModel = $this->_rentOrdersModelFactory->create();
                        $rentOrderModel->setData('order_id', $order->getIncrementId())
                            ->setData('product_id', $items->getProductId())
                            ->setData('product_type', $items->getProductType())
                            ->setData('type', $eventData['event_type']);
                        if ($eventData['event_type'] == 'variable') {
                            $splitdate = explode('|', $eventData['event_date']);
                            $time = explode(' - ', $splitdate[1]);
                            $finalDate = $splitdate[0];
                            $startTime = date('Y-m-d H:i:s', strtotime($finalDate . ' ' . $time[0]));
                            $endTime = date('Y-m-d H:i:s', strtotime($finalDate . ' ' . $time[1]));

                            $rentOrderModel->setData('start_date', $startTime);
                            $rentOrderModel->setData('end_date', $endTime);
                        }
                        $currentDateTime = $this->_bookingHelper->getCurrentDate() . ' ' . $this->_bookingHelper->getCurrentTime();
                        $rentOrderModel->setData('ticket_name', $ticket['name']);
                        $rentOrderModel->setData('created_at', $currentDateTime);
                        $rentOrderModel->setData('qty_ordered', $ticket['qty']);
                        $rentOrderModel->setData('price', $ticket['price']);
                        $rentOrderModel->setData('status', Data::ORDER_STATUS_PENDING);
                        $this->rentOrdersResourceModel->save($rentOrderModel);
                    }
                }
            }
        }
    }
}
