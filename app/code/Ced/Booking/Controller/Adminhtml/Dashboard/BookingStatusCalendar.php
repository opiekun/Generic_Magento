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

namespace Ced\Booking\Controller\Adminhtml\Dashboard;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Backend\App\Action;
use Magento\Framework\App\ObjectManager;

class BookingStatusCalendar extends Action
{
    /**
     * @var \Ced\Booking\Helper\Data
     */
    protected $_helper;

    /**
     * @var JsonFactory
     */
    protected $_jsonFactory;

    public function __construct(Action\Context $context,
                                \Ced\Booking\Helper\Data $helper,
                                \Ced\Booking\Model\ResourceModel\RentOrders\CollectionFactory $orderCollectionFactory,
                                \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    )
    {
        parent::__construct($context);
        $this->_dataHelper = $helper;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_jsonFactory = $jsonFactory;
        if ($this->_dataHelper->isModuleEnabled('Ced_Event')) {
            $this->_eventorderCollectionFactory = ObjectManager::getInstance()
                ->get(\Ced\Event\Model\ResourceModel\Order\CollectionFactory::class);
        }
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $events = [];
        if ($this->_dataHelper->isModuleEnabled('Ced_Event'))
        {
            $eventOrderCollection = $this->_eventorderCollectionFactory->create();
            $eventOrderCollection->getSelect()->group('product_id')
                ->group('order_increment_id');
            if ($eventOrderCollection->count())
            {
                foreach ($eventOrderCollection as $eventOrder)
                {
                    if ($eventOrder->getType() == 'variable')
                    {
                        $start = explode(' ',$eventOrder->getStartDate());
                        $end = explode(' ',$eventOrder->getEndDate());
                        $events[] = ['title' => $eventOrder->getOrderIncrementId().' Booked', 'start' => $start[0], 'end' => $end[0], 'booking_order_id' => $eventOrder->getId(), 'order_type' => 'event'];
                    }
                }
            }
        }
        $rentOrderCollection = $this->_orderCollectionFactory->create();
        if ($rentOrderCollection->count()) {
            foreach ($rentOrderCollection as $order) {
                $start = explode(' ', $order->getStartDate());
                $end = explode(' ', $order->getEndDate());
                $events[] = ['title' => $order->getOrderId() . ' Booked', 'start' => $start[0], 'end' => $end[0], 'booking_order_id' => $order->getId(), 'order_type' => 'rent'];
            }
        }
        $resultJson = $this->_jsonFactory->create();
        return $resultJson->setData($events);

    }
}
