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
use Ced\Event\Model\ResourceModel\Order\Collection;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CreditmemoSaveAfter
 * @package Ced\Booking\Observer
 */
class CreditmemoSaveAfter implements ObserverInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var Data
     */
    protected $_bookingHelper;
    /**
     * InvoiceSaveAfter constructor.
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Data $bookingHelper
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->_bookingHelper = $bookingHelper;
        if ($this->_bookingHelper->isModuleEnabled('Ced_Event')) {
            $this->_eventorderCollection = ObjectManager::getInstance()
                ->get(Collection::class);
        }
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $allItems = $creditmemo->getOrder()->getAllItems();
        $orderIncrementId = $creditmemo->getOrder()->getIncrementId();
        foreach ($allItems as $item) {
            if ($item->getProductType() == Data::APPOINTMENT_PRODUCT_TYPE || $item->getProductType() == Data::RENTAL_PRODUCT_TYPE) {
                if ($item->getQtyRefunded() > 0) {
                    $collection = $this->collectionFactory->create();
                    $collection->addFieldToFilter('order_id', $orderIncrementId)->addFieldToFilter('product_id', $item->getProduct()->getId());
                    if ($item->getQtyRefunded() < $collection->getFirstItem()->getQtyOrdered()) {
                        $status = Data::ORDER_STATUS_PARTIALLY_REFUNDED;
                    } else {
                        $status = Data::ORDER_STATUS_CLOSED;
                    }
                    $collection->getFirstItem()->setStatus($status)
                        ->setQtyRefunded($item->getQtyRefunded())
                        ->save();
                }
            } elseif ($item->getProductType() == Data::EVENT_PRODUCT_TYPE && $this->_bookingHelper->isModuleEnabled('Ced_Event')) {
                if ($item->getQtyRefunded() > 0) {
                    $this->_eventorderCollection->addFieldToFilter('order_increment_id', $orderIncrementId)
                        ->addFieldToFilter('product_id', $item->getProduct()->getId());
                    $this->_eventorderCollection->setStatus(Data::ORDER_STATUS_REFUNDED)
                        ->save();
                }
            }
        }
        return $this;
    }
}
