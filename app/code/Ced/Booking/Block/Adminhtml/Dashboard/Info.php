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

use Magento\Backend\Block\Template;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

class Info extends Template
{


    public $_localeCurrency;

    function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        Timezone $timezone,
        PriceHelper $priceHelper,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Grid\CollectionFactory $collection,
        \Magento\Framework\Locale\Currency $localeCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = []
    )
    {
        $this->_timezone = $timezone;
        $this->_priceHelper = $priceHelper;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->collection = $collection;
        $this->_localeCurrency = $localeCurrency;
        $this->_storeManagerInterface = $storeManagerInterface;
        $this->_priceCurrency = $priceCurrency;
        parent::__construct($context, $data);
    }

    /**
     * Get pending amount data
     *
     * @return Array
     */
    public function getPendingAmount()
    {
        // Total Pending Amount
        $pendingAmount = 0;
        $data = ['total' => $pendingAmount, 'action' => ''];
        $orderCollection = $this->getBookingOrdersCollection();
        if (count($orderCollection) != 0) {
            $orderData = $orderCollection->addFieldToFilter('status', 'pending')->getData();
        }
        if (isset($orderData) && count($orderData) != 0) {
            foreach ($orderData as $pendingData) {
                $pendingAmount = $pendingData['grand_total'] + $pendingAmount;
            }
        }

        if ($pendingAmount > 1000000000000) {
            $pendingAmount = round($pendingAmount / 1000000000000, 4);
            $data['total'] = $this->_localeCurrency->getCurrency($this->_storeManagerInterface->getStore(null)->getBaseCurrencyCode())->toCurrency($pendingAmount) . 'T';

        } elseif ($pendingAmount > 1000000000) {
            $pendingAmount = round($pendingAmount / 1000000000, 4);
            $data['total'] = $this->_localeCurrency->getCurrency($this->_storeManagerInterface->getStore(null)->getBaseCurrencyCode())->toCurrency($pendingAmount) . 'B';

        } elseif ($pendingAmount > 1000000) {
            $pendingAmount = round($pendingAmount / 1000000, 4);
            $data['total'] = $this->_localeCurrency->getCurrency($this->_storeManagerInterface->getStore(null)->getBaseCurrencyCode())->toCurrency($pendingAmount) . 'M';

        } elseif ($pendingAmount > 1000) {
            $pendingAmount = round($pendingAmount / 1000, 4);
            $data['total'] = $this->_localeCurrency->getCurrency($this->_storeManagerInterface->getStore(null)->getBaseCurrencyCode())->toCurrency($pendingAmount) . 'K';

        } else {
            $data['total'] = $this->_priceCurrency->format($pendingAmount);
        }

        return $data;
    }

    /**
     * Get admin's Earned Amount data
     *
     * @return Array
     */
    public function getEarnedAmount()
    {
        // Total Earned Amount
        $data = ['total' => 0, 'action' => ''];
        $netAmount = 0;
        $orderCollection = $this->getBookingOrdersCollection();
        if (count($orderCollection) != 0) {
            $orderData = $orderCollection->addFieldToFilter('status', 'complete')->getData();
        }
        if (isset($orderData) && count($orderData) != 0) {
            foreach ($orderData as $earnedData) {
                $netAmount = $earnedData['grand_total'] + $netAmount;
            }
        }

        if ($netAmount > 1000000000000) {
            $netAmount = round($netAmount / 1000000000000, 4);
            $data['total'] = $this->_localeCurrency->getCurrency($this->_storeManagerInterface->getStore(null)->getBaseCurrencyCode())->toCurrency($netAmount) . 'T';

        } elseif ($netAmount > 1000000000) {
            $netAmount = round($netAmount / 1000000000, 4);
            $data['total'] = $this->_localeCurrency->getCurrency($this->_storeManagerInterface->getStore(null)->getBaseCurrencyCode())->toCurrency($netAmount) . 'B';
        } elseif ($netAmount > 1000000) {
            $netAmount = round($netAmount / 1000000, 4);
            $data['total'] = $this->_localeCurrency->getCurrency($this->_storeManagerInterface->getStore(null)->getBaseCurrencyCode())->toCurrency($netAmount) . 'M';

        } elseif ($netAmount > 1000) {
            $netAmount = round($netAmount / 1000, 4);
            $data['total'] = $this->_localeCurrency->getCurrency($this->_storeManagerInterface->getStore(null)->getBaseCurrencyCode())->toCurrency($netAmount) . 'K';

        } else {


            $data['total'] = $this->_priceCurrency->format($netAmount);
        }

        return $data;
    }

    public function getBookingOrdersCollection()
    {
        $itemscollection = $this->itemCollectionFactory->create();
        $itemscollection->addFieldToFilter('product_type', ['in' => ['appointment', 'hotel','event']]);
        $orderId = $itemscollection->getColumnValues('order_id');
        $unique_order_id = array_unique($orderId);
        $collection = $this->collection->create();
        if (!empty($unique_order_id)) {
            $collection->addFieldToFilter('entity_id', ['in'=>$unique_order_id]);
        }
        return $collection;
    }

    /**
     * Get vendor's Orders Placed data
     *
     * @return Array
     */
    public function getOrdersPlaced()
    {
        // Total Orders Placed
        $order_total = count($this->getBookingOrdersCollection());
        return $order_total;
    }

    /**
     * Get Products Sold data
     *
     * @return Array
     */
    public function getProductsSold()
    {
        // Total[ Products Sold
        $itemscollection = $this->itemCollectionFactory->create();
        $itemscollection->addFieldToFilter('product_type', ['in' => ['appointment', 'hotel','event']]);
        $qtyOrdered = $itemscollection->getColumnValues('qty_ordered');
        $data = ['total' => 0, 'action' => ''];
        $data['total'] = array_sum($qtyOrdered);
        return $data;
    }

}
