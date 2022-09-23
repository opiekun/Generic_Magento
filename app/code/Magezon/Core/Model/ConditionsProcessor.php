<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_Core
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Core\Model;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogInventory\Model\Configuration;
use Magento\Framework\DB\Select;
use Magento\Store\Model\ScopeInterface;
use Zend_Db_Expr;

class ConditionsProcessor
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Model\Config
     */
    protected $_catalogConfig;

    /**
     * @var \Magento\Rule\Model\Condition\Sql\Builder
     */
    protected $sqlBuilder;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $catalogProductVisibility;

    /**
     * @var \Magento\CatalogWidget\Model\Rule
     */
    protected $rule;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Reports\Model\Event\TypeFactory
     */
    protected $_eventTypeFactory;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\CatalogWidget\Model\Rule $rule
     * @param \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Reports\Model\Event\TypeFactory $eventTypeFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Catalog\Model\ResourceModel\Product $product
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Reports\Model\Event\TypeFactory $eventTypeFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Catalog\Model\ResourceModel\Product $product,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\App\Emulation $appEmulation
    ) {
        $this->_storeManager = $storeManager;
        $this->_catalogConfig = $catalogConfig;
        $this->sqlBuilder = $sqlBuilder;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->rule = $rule;
        $this->_resource = $resource;
        $this->_localeDate = $localeDate;
        $this->_eventTypeFactory = $eventTypeFactory;
        $this->_systemStore = $systemStore;
        $this->scopeConfig = $scopeConfig;
        $this->_appEmulation = $appEmulation;
    }

    public function getProductByConditions($model, $store)
    {
        $collection = $this->productCollectionFactory->create();
        $collection->getSelect()->reset(Select::WHERE);
        $collection->setStore($store);
        if (!$this->_storeManager->isSingleStoreMode()) {
            $collection = $this->_addProductAttributesAndPrices($collection);
        }
        $collection->addStoreFilter($store);
        //$collection->addAttributeToSelect('*');
        $conditions = $model->getConditions();
        $conditions->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);
        //$collection->addAttributeToSelect('*');

        switch ($model->getProductType()) {
            case 'latest':
                $collection->getSelect()->order('created_at DESC');
                break;

            case 'new':
                $this->_getNewProductCollection($collection);
                break;

            case 'bestseller':
                $this->_getBestSellerProductCollection($collection, $store->getId());
                break;

            case 'onsale':
                $this->_getOnsaleProductCollection($collection, $store->getId());
                break;

            case 'mostviewed':
                $this->_getMostViewedProductCollection($collection, $store->getId());
                break;

            case 'wishlisttop':
                $this->_getWishlisttopProductCollection($collection, $store->getId());
                break;

            case 'free':
                $collection->addAttributeToFilter('price', ['eq' => 0]);
                break;

            case 'featured':
                $collection->addAttributeToFilter('featured', ['eq' => 1]);
                break;

            case 'toprated':
                $this->_getTopRatedProductCollection($collection, $store->getId());
                break;
        }

        if ($model->getStockStatus() == "1") {
            $this->addOutStockFilterToCollection($collection);
        }

        if ($model->getStockStatus() == "2") {
            $this->addInStockFilterToCollection($collection);
        }

        $collection->joinField(
            'qty',
            'cataloginventory_stock_item',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left'
        );

        if ($ruleStockHigher = $model->getRuleStockHigher()) {
            $collection->addFieldToFilter('qty', ['gteq' => $ruleStockHigher]);
        }

        if ($ruleStockLower = $model->getRuleStockLower()) {
            $collection->addFieldToFilter('qty', ['lteq' => $ruleStockLower]);
        }

        $collection->setVisibility(
            $this->catalogProductVisibility->getVisibleInSiteIds()
        );

        return $collection;
    }

    /**
     * Adds filtering for collection to return only in stock products
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection $collection
     * @return void
     */
    public function addInStockFilterToCollection($collection)
    {
        $manageStock = $this->scopeConfig->getValue(
            Configuration::XML_PATH_MANAGE_STOCK,
            ScopeInterface::SCOPE_STORE
        );
        $cond = [
            '{{table}}.use_config_manage_stock = 0 AND {{table}}.manage_stock=1 AND {{table}}.is_in_stock=1',
            '{{table}}.use_config_manage_stock = 0 AND {{table}}.manage_stock=0'
        ];

        if ($manageStock) {
            $cond[] = '{{table}}.use_config_manage_stock = 1 AND {{table}}.is_in_stock=1';
        } else {
            $cond[] = '{{table}}.use_config_manage_stock = 1';
        }

        $collection->joinField(
            'inventory_in_stock',
            'cataloginventory_stock_item',
            'is_in_stock',
            'product_id=entity_id',
            '(' . join(') OR (', $cond) . ')'
        );

        $collection->addAttributeToFilter('inventory_in_stock', 1);
    }

    /**
     * Adds filtering for collection to return only in stock products
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection $collection
     * @return void
     */
    public function addOutStockFilterToCollection($collection)
    {
        $manageStock = $this->scopeConfig->getValue(
            Configuration::XML_PATH_MANAGE_STOCK,
            ScopeInterface::SCOPE_STORE
        );
        $cond = [
            '{{table}}.use_config_manage_stock = 0 AND {{table}}.manage_stock=1 AND {{table}}.is_in_stock=0',
            '{{table}}.use_config_manage_stock = 0 AND {{table}}.manage_stock=0'
        ];

        if ($manageStock) {
            $cond[] = '{{table}}.use_config_manage_stock = 1 AND {{table}}.is_in_stock=0';
        } else {
            $cond[] = '{{table}}.use_config_manage_stock = 1';
        }

        $collection->joinField(
            'inventory_in_stock',
            'cataloginventory_stock_item',
            'is_in_stock',
            'product_id=entity_id',
            '(' . join(') OR (', $cond) . ')'
        );

        $collection->addAttributeToFilter('inventory_in_stock', 0);
    }

    protected function _getTopRatedProductCollection($collection, $storeId)
    {
        $collection->joinField(
            'review',
            $this->_resource->getTableName('review_entity_summary'),
            'reviews_count',
            'entity_pk_value=entity_id',
            'at_review.store_id=' . (int)$storeId,
            'review > 0',
            'left'
        );
        $collection->getSelect()->order(['review DESC', 'e.created_at']);
    }

    protected function _getNewProductCollection($collection)
    {
        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        $collection->addAttributeToFilter(
            'news_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['is' => new Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            'news_to_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayStartOfDayDate],
                    1 => ['is' => new Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            [
                ['attribute' => 'news_from_date', 'is' => new Zend_Db_Expr('not null')],
                ['attribute' => 'news_to_date', 'is' => new Zend_Db_Expr('not null')],
            ]
        )->addAttributeToSort(
            'news_from_date',
            'desc'
        );
    }

    protected function _getBestSellerProductCollection($collection, $storeId)
    {
        $collection->getSelect()
            ->join(
                [
                    'aggregation' => $this->_resource->getTableName('sales_bestsellers_aggregated_monthly'),
                ],
                "e.entity_id = aggregation.product_id AND aggregation.store_id={$storeId} AND qty_ordered >0",
                [
                    'sold_quantity' => 'SUM(aggregation.qty_ordered)'
                ]
            )
            ->group('e.entity_id')
            ->order(['sold_quantity DESC', 'e.created_at']);
    }

    protected function _getWishlisttopProductCollection($collection, $storeId)
    {
        $eventTypes = $this->_eventTypeFactory->create()->getCollection();
        foreach ($eventTypes as $eventType) {
            if ($eventType->getEventName() == 'wishlist_add_product') {
                $wishlistEvent = (int)$eventType->getId();
                break;
            }
        }

        $collection->getSelect()
            ->join(
                [
                    'report_table_views' => $this->_resource->getTableName('report_event'),
                ],
                "e.entity_id = report_table_views.object_id AND report_table_views.event_type_id = " . $wishlistEvent,
                [
                    'views' => 'COUNT(report_table_views.event_id)'
                ]
            )
            ->group('e.entity_id')
            ->order(['views DESC'])
            ->having(
                'COUNT(report_table_views.event_id) > ?',
                0
            );
    }

    protected function _getMostViewedProductCollection($collection, $storeId)
    {
        $eventTypes = $this->_eventTypeFactory->create()->getCollection();
        foreach ($eventTypes as $eventType) {
            if ($eventType->getEventName() == 'catalog_product_view') {
                $productViewEvent = (int)$eventType->getId();
                break;
            }
        }

        $collection->getSelect()
            ->join(
                [
                    'report_table_views' => $this->_resource->getTableName('report_event'),
                ],
                "e.entity_id = report_table_views.object_id AND report_table_views.event_type_id = " . $productViewEvent,
                [
                    'views' => 'COUNT(report_table_views.event_id)'
                ]
            )
            ->group('e.entity_id')
            ->order(['views DESC', 'e.created_at'])
            ->having(
                'COUNT(report_table_views.event_id) > ?',
                0
            );
    }

    protected function _getOnsaleProductCollection($collection, $storeId)
    {
        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        $collection->addAttributeToFilter(
            'special_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['is' => new Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            'special_to_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayStartOfDayDate],
                    1 => ['is' => new Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            [
                ['attribute' => 'special_from_date', 'is' => new Zend_Db_Expr('not null')],
                ['attribute' => 'special_to_date', 'is' => new Zend_Db_Expr('not null')],
            ]
        )->addAttributeToSort(
            'special_from_date',
            'desc'
        );

        $collection->getSelect()->where('final_price < price');
    }

    /**
     * Add all attributes and apply pricing logic to products collection
     * to get correct values in different products lists.
     * E.g. crosssells, upsells, new products, recently viewed
     *
     * @param Collection $collection
     * @return Collection
     */
    protected function _addProductAttributesAndPrices(
        Collection $collection
    ) {
        return $collection
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
            ->addUrlRewrite();
    }
}
