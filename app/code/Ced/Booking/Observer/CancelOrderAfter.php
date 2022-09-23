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
use Ced\Booking\Helper\Data;
use Magento\Framework\App\ObjectManager;

/**
 * Class CancelOrderAfter
 * @package Ced\Booking\Observer
 */
class CancelOrderAfter implements ObserverInterface
{
    /**
     * CancelOrderAfter constructor.
     * @param \Ced\Booking\Model\ResourceModel\RentOrders\CollectionFactory $collectionFactory
     * @param Data $dataHelper
     */
    public function __construct(\Ced\Booking\Model\ResourceModel\RentOrders\CollectionFactory $collectionFactory,
                                Data $dataHelper)
    {
        $this->collectionFactory = $collectionFactory;
        $this->_dataHelper = $dataHelper;
        if ($this->_dataHelper->isModuleEnabled('Ced_Event')) {
            $this->_eventorderCollection = ObjectManager::getInstance()
                ->get(\Ced\Event\Model\ResourceModel\Order\Collection::class);
        }
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $rentOrderCollection = $this->collectionFactory->create();
        $rentOrderCollection->addFieldToFilter('order_id',$order->getIncrementId());
        if (!empty($rentOrderCollection)) {
            foreach ($rentOrderCollection as $rentOrder) {
                $rentOrder->setData('status',Data::ORDER_STATUS_CANCELLED);
            }
            $rentOrderCollection->save();
        }

        if ($this->_dataHelper->isModuleEnabled('Ced_Event')) {
            $this->_eventorderCollection->addFieldToFilter('order_id', $order->getIncrementId());
            if ($this->_eventorderCollection->count()) {
                foreach ($this->_eventorderCollection as $order) {
                    $order->setData('status', Data::ORDER_STATUS_CANCELLED);
                }
                $this->_eventorderCollection->save();
            }
        }
        return $this;
    }

}



