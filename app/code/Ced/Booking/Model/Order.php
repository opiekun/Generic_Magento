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
 * @category  Ced
 * @package   Ced_Booking
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license   https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Booking\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\Order\ProductOption;
/**
 * Class Order
 * @package Ced\Booking\Model
 */
class Order extends \Magento\Sales\Model\Order
{
    /**
     * Order constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Sales\Api\InvoiceManagementInterface $invoiceManagement
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Sales\Model\Order\Status\HistoryFactory $orderHistoryFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $addressCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory $paymentCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $memoCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productListFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @param ResolverInterface|null $localeResolver
     * @param null $productOption
     * @param OrderItemRepositoryInterface|null $itemRepository
     * @param SearchCriteriaBuilder|null $searchCriteriaBuilder
     */
    public function __construct(\Magento\Framework\Model\Context $context,
                                \Magento\Framework\Registry $registry,
                                \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
                                AttributeValueFactory $customAttributeFactory,
                                \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
                                \Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Magento\Sales\Model\Order\Config $orderConfig,
                                \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
                                \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory,
                                \Magento\Catalog\Model\Product\Visibility $productVisibility,
                                \Magento\Sales\Api\InvoiceManagementInterface $invoiceManagement,
                                \Magento\Directory\Model\CurrencyFactory $currencyFactory,
                                \Magento\Eav\Model\Config $eavConfig,
                                \Magento\Sales\Model\Order\Status\HistoryFactory $orderHistoryFactory,
                                \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $addressCollectionFactory,
                                \Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory $paymentCollectionFactory,
                                \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory,
                                \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
                                \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory,
                                \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $memoCollectionFactory,
                                \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory,
                                \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory,
                                PriceCurrencyInterface $priceCurrency,
                                \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productListFactory,
                                \Ced\Booking\Helper\Data $helperData,
                                \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
                                \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
                                array $data = [],
                                ResolverInterface $localeResolver = null,
                                ProductOption $productOption = null,
                                OrderItemRepositoryInterface $itemRepository = null,
                                SearchCriteriaBuilder $searchCriteriaBuilder = null)
    {
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $timezone, $storeManager,
            $orderConfig, $productRepository, $orderItemCollectionFactory, $productVisibility, $invoiceManagement,
            $currencyFactory, $eavConfig, $orderHistoryFactory, $addressCollectionFactory, $paymentCollectionFactory,
            $historyCollectionFactory, $invoiceCollectionFactory, $shipmentCollectionFactory, $memoCollectionFactory,
            $trackCollectionFactory, $salesOrderCollectionFactory, $priceCurrency, $productListFactory, $resource,
            $resourceCollection, $data, $localeResolver, $productOption, $itemRepository, $searchCriteriaBuilder);
        $this->_helperData = $helperData;
        $this->_orderItemCollectionFactory = $orderItemCollectionFactory;
    }

    /**
     * @return \Magento\Sales\Model\ResourceModel\Order\Item\Collection
     */
    private function bookingProductInOrder()
    {
        $orderId = $this->getId();
        $bookingTypes = $this->_helperData->getAllBookingTypes();
        $orderItemCollection = $this->_orderItemCollectionFactory->create();
        $orderItemCollection->addFieldToFilter('order_id',$orderId)
            ->addFieldToFilter('product_type',['in'=>$bookingTypes]);
        return $orderItemCollection;
    }

    /**
     * Retrieve order reorder availability
     *
     * @param bool $ignoreSalable
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _canReorder($ignoreSalable = false)
    {
        if ($this->bookingProductInOrder()->count()>0)
            return false;

        if ($this->canUnhold() || $this->isPaymentReview()) {
            return false;
        }

        if ($this->getActionFlag(self::ACTION_FLAG_REORDER) === false) {
            return false;
        }

        $products = [];
        $itemsCollection = $this->getItemsCollection();
        foreach ($itemsCollection as $item) {
            $products[] = $item->getProductId();
        }
        if (!empty($products)) {
            $productsCollection = $this->productListFactory->create()
                ->setStoreId($this->getStoreId())
                ->addIdFilter($products)
                ->addAttributeToSelect('status')
                ->load();

            foreach ($itemsCollection as $item) {
                $product = $productsCollection->getItemById($item->getProductId());
                if (!$product) {
                    return false;
                }
                if (!$ignoreSalable && !$product->isSalable()) {
                    return false;
                }
            }
        }
        return true;
    }
}
