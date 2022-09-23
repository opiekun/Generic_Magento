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

use Magento\CatalogInventory\Model\ResourceModel\Stock\StatusFactory;

class ProductList
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
     * @var \Magento\Reports\Model\Event\TypeFactory
     */
    protected $_eventTypeFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Widget\Helper\Conditions
     */
    protected $conditionsHelper;

    /**
     * @var StatusFactory
     */
    protected $stockStatusFactory;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface                     $storeManager             
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory 
     * @param \Magento\Catalog\Model\Product\Visibility                      $catalogProductVisibility 
     * @param \Magento\Catalog\Model\Config                                  $catalogConfig            
     * @param \Magento\CatalogWidget\Model\Rule                              $rule                     
     * @param \Magento\Rule\Model\Condition\Sql\Builder                      $sqlBuilder               
     * @param \Magento\Framework\App\ResourceConnection                      $resource                 
     * @param \Magento\Reports\Model\Event\TypeFactory                       $eventTypeFactory         
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface           $localeDate               
     * @param \Magento\Widget\Helper\Conditions                              $conditionsHelper         
     * @param StatusFactory                                                  $stockStatusFactory       
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
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        StatusFactory $stockStatusFactory
    ) {
        $this->_storeManager            = $storeManager;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->_catalogConfig           = $catalogConfig;
        $this->rule                     = $rule;
        $this->sqlBuilder               = $sqlBuilder;
        $this->_resource                = $resource;
        $this->_eventTypeFactory        = $eventTypeFactory;
        $this->_localeDate              = $localeDate;
        $this->conditionsHelper         = $conditionsHelper;
        $this->stockStatusFactory       = $stockStatusFactory;
    }

    public function getProductCollection($source = 'latest', $numberItems = 8, $order = 'newestfirst', $conditions = '', $storeId = \Magento\Store\Model\Store::DEFAULT_STORE_ID, $showOutStock = true)
    {
        if ($storeId) {
            $store = $this->_storeManager->getStore($storeId);
        } else {
            $store = $this->_storeManager->getStore();
        }
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToFilter('visibility', $this->catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection)->addStoreFilter($store);
        if ($conditions) {
            $conditions = $this->conditionsHelper->decode($conditions);
            foreach ($conditions as $key => $condition) {
                    if (!empty($condition['attribute'])
                        && in_array($condition['attribute'], ['special_from_date', 'special_to_date'])
                    ) {
                        $conditions[$key]['value'] = date('Y-m-d H:i:s', strtotime($condition['value']));
                }
            }
            $this->rule->loadPost(['conditions' => $conditions]);
            $conditions = $this->rule->getConditions();
            $conditions->collectValidatedAttributes($collection);
            $this->sqlBuilder->attachConditionToCollection($collection, $conditions);
        }
        $collection->setPageSize($numberItems);

        $stockFlag = 'has_stock_status_filter';
        if (!$showOutStock && $collection->hasFlag($stockFlag)) {
            $resource = $this->stockStatusFactory->create();
            $resource->addStockDataToCollection(
                $collection,
                false
            );
            $collection->setFlag($stockFlag, true);
        }

        switch ($source) {
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
            $collection->getSelect()->where('price_index.price = ?', 0);
            $collection->addAttributeToFilter('type_id', [
                'in' => [
                    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
                    \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
                    \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE
                ]
            ]);
            break;

            case 'featured':
            $collection->addAttributeToFilter('featured', ['eq' => 1]);
            break;

            case 'toprated':
            $this->_getTopRatedProductCollection($collection, $store->getId());
            break;

            case 'random':
            $collection->getSelect()->order('RAND()');
            break;
        }

        if ($order!='default') {
            switch ($order) {
                case 'alphabetically':
                    $collection->getSelect()->reset(\Zend_Db_Select::ORDER);
                    $collection->setOrder('name', 'ASC');
                    // usort($items, function($a, $b) {
                    //     return $a['name'] > $b['name'];
                    // });
                    break;

                case 'price_low_to_high':
                    $collection->getSelect()->reset(\Zend_Db_Select::ORDER);
                    $collection->setOrder('price', 'ASC');
                    // usort($items, function($a, $b) {
                    //     return $a['price'] > $b['price'];
                    // });
                    break;

                case 'price_high_to_low':
                    $collection->getSelect()->reset(\Zend_Db_Select::ORDER);
                    $collection->setOrder('price', 'DESC');
                    // usort($items, function($a, $b) {
                    //     return $a['price'] < $b['price'];
                    // });
                    break;

                case 'random':
                    $collection->getSelect()->reset(\Zend_Db_Select::ORDER);
                    $collection->getSelect()->order('RAND()');
                    break;

                case 'newestfirst':
                    $collection->getSelect()->reset(\Zend_Db_Select::ORDER);
                    $collection->setOrder('entity_id', 'DESC');
                    // usort($items, function($a, $b) {
                    //     $aval = strtotime($a['created_at']);
                    //     $bval = strtotime((int) $b['created_at']);
                    //     if ($aval == $bval) {
                    //         return 0;
                    //     }
                    //     return $aval < $bval ? 1 : -1;
                    // });
                    break;

                case 'oldestfirst':
                    $collection->getSelect()->reset(\Zend_Db_Select::ORDER);
                    $collection->setOrder('entity_id', 'ASC');
                    // usort($items, function($a, $b) {
                    //     $aval = strtotime($a['created_at']);
                    //     $bval = strtotime((int) $b['created_at']);
                    //     if ($aval == $bval) {
                    //         return 0;
                    //     }
                    //     return $aval > $bval ? 1 : -1;
                    // });
                    break;

                case 'product_attr':
                    $collection->setOrder('product_position', 'ASC');
                    // usort($items, function($a, $b) {
                    //     return (isset($a['product_position']) ? (int) $a['product_position'] : 0) > (isset($b['product_position']) ? (int) $b['product_position'] : 0);
                    // });
                    break;
            }
        }

        $items = $collection->getItems();

        return $items;
    }

    protected function _getTopRatedProductCollection($collection, $storeId)
    {
        $collection->joinField(
            'review',
            $this->_resource->getTableName('review_entity_summary'),
            'reviews_count',
            'entity_pk_value=entity_id',
            'at_review.store_id=' . (int) $storeId,
            'review > 0',
            'left'
            );
        $collection->getSelect()->order(['review DESC', 'e.created_at']);
    }

    protected function _getNewProductCollection($collection)
    {
        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate   = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        $collection->addAttributeToFilter(
            'news_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            'news_to_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayStartOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            [
                ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
                ['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
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
            )->group(
                'e.entity_id'
            )->order([
                'sold_quantity DESC',
                'e.created_at'
            ]);
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
                'e.entity_id = report_table_views.object_id AND report_table_views.event_type_id = ' . $wishlistEvent,
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
                'e.entity_id = report_table_views.object_id AND report_table_views.event_type_id = ' . $productViewEvent,
            [
                'views' => 'COUNT(report_table_views.event_id)'
            ]
        )->group(
            'e.entity_id'
        )->order([
            'views DESC',
            'e.created_at'
        ])->having(
            'COUNT(report_table_views.event_id) > ?',
            0
        );
    }

    protected function _getOnsaleProductCollection($collection, $storeId)
    {
        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate   = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        $collection->addAttributeToFilter(
            'special_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            'special_to_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayStartOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            [
                ['attribute' => 'special_from_date', 'is' => new \Zend_Db_Expr('not null')],
                ['attribute' => 'special_to_date', 'is' => new \Zend_Db_Expr('not null')],
            ]
        )->addAttributeToSort(
            'special_from_date',
            'desc'
        );

        $collection->getSelect()->where('price_index.final_price < price_index.price');
    }

    /**
     * Add all attributes and apply pricing logic to products collection
     * to get correct values in different products lists.
     * E.g. crosssells, upsells, new products, recently viewed
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _addProductAttributesAndPrices(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    ) {
        $collection->addTaxPercents()
        ->addMinimalPrice()
        ->addFinalPrice()
        ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
        ->addUrlRewrite();
        return $collection;
    }
}