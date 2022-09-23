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
use Ced\Booking\Model\ResourceModel\RentOrders\CollectionFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\Helper\Data as JsonHelperData;

/**
 * Class CartAddBefore
 * @package Ced\Booking\Observer
 */
class CartAddBefore implements ObserverInterface
{
    /**
     * @var Cart
     */
    protected $checkoutSession;

    /**
     * @var JsonHelperData
     */
    protected $_jsonHelper;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var Data
     */
    protected $_bookingHelper;

    /**
     * @var CollectionFactory
     */
    protected $_rentorderCollectionFactory;

    /**
     * CartAddBefore constructor.
     * @param Cart $checkoutSession
     * @param JsonHelperData $jsonHelper
     * @param ProductRepository $productRepository
     * @param Http $request
     * @param Data $bookingHelper
     * @param CollectionFactory $rentorderCollectionFactory
     */
    public function __construct(
        Cart $checkoutSession,
        JsonHelperData $jsonHelper,
        ProductRepository $productRepository,
        Http $request,
        Data $bookingHelper,
        CollectionFactory $rentorderCollectionFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->_jsonHelper = $jsonHelper;
        $this->productRepository = $productRepository;
        $this->request = $request;
        $this->_bookingHelper = $bookingHelper;
        $this->_rentorderCollectionFactory = $rentorderCollectionFactory;
        $objectManager = ObjectManager::getInstance();
        if ($this->_bookingHelper->isModuleEnabled('Ced_Event')) {
            $this->eventOrder = $objectManager->create(\Ced\Event\Model\Order::class);
        }
        if ($this->_bookingHelper->isModuleEnabled('Ced_Rental')) {
            $this->rentalHelper = $objectManager->create(\Ced\Rental\Helper\Data::class);
        }
        if ($this->_bookingHelper->isModuleEnabled('Ced_Appointment')) {
            $this->appointmentHelper = $objectManager->create(\Ced\Appointment\Helper\Data::class);
        }
    }

    /**
     * @param EventObserver $observer
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(EventObserver $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $allItems = $this->checkoutSession->getQuote()->getAllVisibleItems();
        $postData = $this->request->getPostValue();

        if (isset($postData['product'])) {
            if ($product->getTypeId() == Data::EVENT_PRODUCT_TYPE && $this->_bookingHelper->isModuleEnabled('Ced_Event')) {
                if (!isset($postData['event_date']) || $postData['event_date'] == __('Please select date')) {
                    throw new LocalizedException(__('Please select event date and time.'));
                } elseif (!isset($postData['event_ticket']) || empty($postData['event_ticket'])) {
                    throw new LocalizedException(__('Please select ticket.'));
                } elseif (!empty($postData['event_ticket'])) {
                    $ticketSelected = false;
                    foreach ($postData['event_ticket'] as $ticket) {
                        if ($ticket['qty'] > 0) {
                            $ticketSelected = true;
                        }
                    }
                    if (!$ticketSelected) {
                        throw new LocalizedException(__('Please select ticket.'));
                    }
                }
            } elseif ($product->getTypeId() == Data::RENTAL_PRODUCT_TYPE && $this->_bookingHelper->isModuleEnabled('Ced_Rental')) {
                $response = $this->rentalHelper->availabilityCheck($allItems, $postData);
                if ($response['qty'] < 0) {
                    throw new LocalizedException(__('Not enough quantity.'));
                }
            } elseif ($product->getTypeId() == Data::APPOINTMENT_PRODUCT_TYPE && $this
                    ->_bookingHelper
                    ->isModuleEnabled('Ced_Appointment')) {
                $leftQty = $this->appointmentHelper->checkAvailability($allItems, $postData);
                if ($leftQty < 0) {
                    throw new LocalizedException(__('Not enough quantity.'));
                }
            }

            $product = $this
                ->productRepository
                ->getById($postData['product']);
            if ($product->getTypeId() == Data::EVENT_PRODUCT_TYPE) {
                $quoteTicket = [];
                foreach ($allItems as $item) {
                    if ($item->getProduct()->getId() == $postData['product']) {
                        $productOptions = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                        $options = $productOptions['info_buyRequest'];
                        if ($item->getProduct()
                                ->getTypeId() == 'event' && $this
                                ->_bookingHelper
                                ->isModuleEnabled('Ced_Event')) {
                            $eventData = $options;
                            if ($postData['event_type'] == 'variable') {
                                if ($eventData['event_date'] == $postData['event_date']) {
                                    if (!empty($eventData['event_ticket'])) {
                                        foreach ($eventData['event_ticket'] as $addedTicket) {
                                            if (!empty($postData['event_ticket'])) {
                                                foreach ($postData['event_ticket'] as $ticketData) {
                                                    if ($ticketData['name'] == $addedTicket['name'] && $ticketData['qty'] > 0) {
                                                        $quoteTicket[$ticketData['name']]['quote_qty'][] = $addedTicket['qty'];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                if (!empty($eventData['event_ticket'])) {
                                    foreach ($eventData['event_ticket'] as $addedTicket) {
                                        if (!empty($postData['event_ticket'])) {
                                            foreach ($postData['event_ticket'] as $ticketData) {
                                                if ($ticketData['name'] == $addedTicket['name'] && $ticketData['qty'] > 0) {
                                                    $quoteTicket[$ticketData['name']]['quote_qty'][] = $addedTicket['qty'];
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $eventTickets = $product->getEventTickets();
                if ($eventTickets) {
                    $eventTicketsArray = $this->_jsonHelper->jsonDecode($eventTickets);
                }

                if (!empty($postData['event_ticket'])) {
                    foreach ($postData['event_ticket'] as $ticketData) {
                        $quoteTotalQty = 0;
                        if ($ticketData['qty'] > 0) {
                            if (!empty($quoteTicket)) {
                                foreach ($quoteTicket as $name => $qty) {
                                    if ($name = $ticketData['name']) {
                                        $quoteTotalQty = array_sum($qty['quote_qty']);
                                    }
                                }
                            }
                            $availablTicket = $this->isTicketAvailable($eventTicketsArray, $ticketData['name'], $postData, $ticketData['qty'], $quoteTotalQty);
                            if ($availablTicket['success'] == false) {
                                throw new LocalizedException(__($availablTicket['msg']));
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $eventTicketsArray
     * @param $name
     * @param $postData
     * @param $ticketQty
     * @param $quoteTotalQty
     * @return array
     */
    protected function isTicketAvailable($eventTicketsArray, $name, $postData, $ticketQty, $quoteTotalQty)
    {
        $result = [];
        $orderedQty = [];
        $totalQty = $this->getTicketQtyByName($eventTicketsArray, $name);
        if ($postData['event_type'] == 'fixed') {
            $orderedQty = $this->eventOrder->eventOrders($postData['event_type'], $postData['product'], $name)->getColumnValues('qty_ordered');
        } elseif ($postData['event_type'] == 'variable') {
            $date = $postData['event_date'];
            $splitdate = explode('|', $date);
            $time = explode(' - ', $splitdate[1]);
            $finalDate = $splitdate[0];
            $startTime = date('Y-m-d H:i:s', strtotime($finalDate . ' ' . $time[0]));
            $endTime = date('Y-m-d H:i:s', strtotime($finalDate . ' ' . $time[1]));

            $orderedQty = $this->eventOrder->eventOrders($postData['event_type'], $postData['product'], $name, $startTime, $endTime)->getColumnValues('qty_ordered');
        }
        $totalOrderedQty = array_sum($orderedQty);
        $remainingQtyExceptCurrent = (int)$totalQty - ((int)$totalOrderedQty + (int)$quoteTotalQty);
        $remainingQty = (int)$totalQty - ((int)$totalOrderedQty + (int)$quoteTotalQty + (int)$ticketQty);
        if ($remainingQty < 0) {
            $result['success'] = false;
            if ($remainingQtyExceptCurrent > 0) {
                $result['msg'] = __('Only ') . $remainingQtyExceptCurrent . __(' ticket available for ') . $name . ' .';
            } else {
                $result['msg'] = $name . __(' ticket not available.');
            }
        } else {
            $result['success'] = true;
        }
        return $result;
    }

    /**
     * @param $ticketData
     * @param $ticketName
     * @return int
     */
    protected function getTicketQtyByName($ticketData, $ticketName)
    {
        $qty = 0;
        if ($ticketData) {
            foreach ($ticketData as $key => $ticket) {
                if ($ticket['ticket_name'] == $ticketName) {
                    $qty = $ticket['ticket_qty'];
                }
            }
        }
        return $qty;
    }
}
