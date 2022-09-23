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
 * @package     Ced_Event
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Booking\CustomerData;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Class LastOrderedItems
 * @package Ced\Booking\CustomerData
 */
class LastOrderedItems extends \Magento\Sales\CustomerData\LastOrderedItems
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * LastOrderedItems constructor.
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     * @param LoggerInterface $logger
     */
    public function __construct(\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
                                \Magento\Sales\Model\Order\Config $orderConfig,
                                \Magento\Customer\Model\Session $customerSession,
                                \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
                                \Magento\Store\Model\StoreManagerInterface $storeManager,
                                ProductRepositoryInterface $productRepository,
                                LoggerInterface $logger,
                                \Ced\Booking\Helper\Data $helperData)
    {
        parent::__construct($orderCollectionFactory, $orderConfig, $customerSession, $stockRegistry, $storeManager, $productRepository, $logger);
        $this->_storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
        $this->_helperData = $helperData;
    }

    /**
     * Get list of last ordered products
     *
     * @return array
     */
    protected function getItems()
    {
        $bookingTypes = $this->_helperData->getAllBookingTypes();
        $items = [];
        $order = $this->getLastOrder();
        $limit = self::SIDEBAR_ORDER_LIMIT;
        if ($order) {
            $website = $this->_storeManager->getStore()->getWebsiteId();
            /** @var \Magento\Sales\Model\Order\Item $item */
            foreach ($order->getParentItemsRandomCollection($limit) as $item) {

                /** @var \Magento\Catalog\Model\Product $product */
                try {
                    $product = $this->productRepository->getById(
                        $item->getProductId(),
                        false,
                        $this->_storeManager->getStore()->getId()
                    );
                } catch (NoSuchEntityException $noEntityException) {
                    $this->logger->critical($noEntityException);
                    continue;
                }
                if (isset($product) && in_array($website, $product->getWebsiteIds())) {
                    if (!in_array($product->getTypeId(),$bookingTypes)) {
                        $url = $product->isVisibleInSiteVisibility() ? $product->getProductUrl() : null;
                        $items[] = [
                            'id' => $item->getId(),
                            'name' => $item->getName(),
                            'url' => $url,
                            'is_saleable' => $this->isItemAvailableForReorder($item),
                        ];
                    }
                }
            }
        }

        return $items;
    }
}
