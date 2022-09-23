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
namespace Ced\Booking\Block\Adminhtml\Dashboard;

use Magento\Framework\App\ObjectManager;

class BookingDetails extends \Magento\Backend\Block\Template
{

    protected  $_helperData;
    protected $orderRepository;

    /**
     * BookingDetails constructor.
     * @param \Ced\Booking\Helper\Data $helperData
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Ced\Booking\Model\RentOrdersFactory $rentOrdersFactory
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param array $data
     */
    public function __construct(
        \Ced\Booking\Helper\Data $helperData,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Ced\Booking\Model\RentOrdersFactory $rentOrdersFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        array $data = array())
    {
        $this->orderRepository = $order;
        $this->_helperData = $helperData;
        $this->_rentOrdersFactory = $rentOrdersFactory;
        $this->_productRepository = $productRepository;
        parent::__construct($context, $data);

        if ($this->_helperData->isModuleEnabled('Ced_Event')) {
            $this->_eventorderFactory = ObjectManager::getInstance()
                ->get(\Ced\Event\Model\OrderFactory::class);
            $this->_eventorderCollectionFactory = ObjectManager::getInstance()
                ->get(\Ced\Event\Model\ResourceModel\Order\CollectionFactory::class);

        }
    }


    public function getOrderDetails(){

        $bookingOrderId = $this->getOrderId();
        $orderType = $this->getOrderType();
        $data = [];
        if ($orderType == 'event' && $this->_helperData->isModuleEnabled('Ced_Event')) {

            $eventorder = $this->_eventorderFactory->create();
            $bookingData = $eventorder->load($bookingOrderId);
            $orderCollection = $this->_eventorderCollectionFactory->create();
            $orderCollection->addFieldToFilter('product_id',$bookingData->getProductId())
                ->addFieldToFilter('order_increment_id',$bookingData->getOrderIncrementId());
            $order = $this->orderRepository->loadByIncrementId($bookingData->getOrderIncrementId());
            $data['order_id'] = $bookingData->getOrderIncrementId();
            $data['tickets'] = $orderCollection->getData();
        } else {
            $rentOrders = $this->_rentOrdersFactory->create();
            $bookingData = $rentOrders->load($bookingOrderId);
            $order = $this->orderRepository->loadByIncrementId($bookingData->getOrderId());
            $data['order_id'] = $bookingData->getOrderId();
        }

        $product =  $this->_productRepository->getById($bookingData->getProductId());

        $data['order_entity_id'] =$order->getId();

        $data['order_created_date'] = $order->getcreatedAt();
        $data['product_name'] = $product->getName();
        $data['booking_from'] = $bookingData->getStartDate();
        $data['booking_to'] = $bookingData->getEndDate();
        $data['product_type'] = $bookingData->getProductType();
        if ($orderType == 'room')
        {
            $data['booking_qty'] = count($bookingData->getData());
        } elseif ($orderType != 'event') {
            $data['booking_qty'] = $bookingData->getQty();
        }
        $data['booking_status'] = $order->getStatus();
        $data['product_id'] = $product->getId();
        return $data;
    }
}
