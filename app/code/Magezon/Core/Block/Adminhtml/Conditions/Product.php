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

namespace Magezon\Core\Block\Adminhtml\Conditions;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Registry;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\ObjectManager;
use Magezon\Core\Model\ConditionsProcessor;

class Product extends Extended
{
    /**
     * Cache group Tag
     */
    const CACHE_GROUP = \Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER;
    const CACHE_TAG   = \Magento\Framework\App\Cache\Type\Config::CACHE_TAG;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var ConditionsProcessor
     */
    protected $prosessor;

    /**
     * @var array
     */
    protected $_cacheManager;

    /**
     * @var \Magezon\Core\Helper\Data
     */
    protected $coreHelper;

    /**
     * @var Status
     */
    protected $_status;

    /**
     * @var Visibility
     */
    protected $_visibility;

    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $cacheState;

    /**
     * Product constructor.
     * @param Context $context
     * @param Data $backendHelper
     * @param Status $status
     * @param Visibility $visibility
     * @param ProductFactory $productFactory
     * @param Registry $coreRegistry
     * @param ResourceConnection $resource
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magezon\Core\Helper\Data $coreHelper
     * @param ConditionsProcessor $processor
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        Status $status,
        Visibility $visibility,
        ProductFactory $productFactory,
        Registry $coreRegistry,
        ResourceConnection $resource,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magezon\Core\Helper\Data $coreHelper,
        ConditionsProcessor $processor,
        array $data = []
    ) {
        $this->resource = $resource;
        $this->_status = $status;
        $this->_visibility = $visibility;
        $this->_productFactory = $productFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->systemStore = $systemStore;
        $this->cacheState = $cacheState;
        $this->coreHelper = $coreHelper;
        $this->prosessor = $processor;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('mgz_conditions_product');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    /**
     * @return \Magento\Rule\Model\AbstractModel
     */
    public function getModel()
    {
        return $this->_coreRegistry->registry('mgz_conditions_model');
    }

    /**
     * @return int
     */
    public function getGridId()
    {
        return $this->getRequest()->getParam('mgz_conditions_grid_id');
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        $productIds = $this->getProductIds();
        $collection = $this->_productFactory->create()->getCollection();
        $collection->addAttributeToSelect(
            ['name', 'url_key', 'visibility', 'status', 'price', 'small_image', 'created_at']
        );
        $tableName = $collection->getTable('cataloginventory_stock_item');
        $collection->getSelect()->joinLeft(
            ['_inventory_table' => $tableName],
            "_inventory_table.product_id = e.entity_id",
            ['is_in_stock']
        );
        $this->setCollection($collection);
        if (empty($productIds)) {
            $productIds = 0;
        }
        $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
        $sortOrder = $this->getModel()->getSortOrder();
        if ($sortOrder != null) {
            switch ($sortOrder) {
                case '0':
                    $this->getCollection()->addAttributeToSort('entity_id', 'ASC');
                    break;
                case '1':
                    $this->getCollection()->getSelect()->order('created_at DESC');
                    break;
                case '2':
                    $this->getCollection()->getSelect()->order(['is_in_stock DESC']);
                    break;
                case '3':
                    $this->getCollection()->addAttributeToSort('name', 'ASC');
                    break;
                case '4':
                    $this->getCollection()->addAttributeToSort('name', 'DESC');
                    break;
                case '5':
                    $this->getCollection()->addAttributeToSort('price', 'ASC');
                    break;
                case '6':
                    $this->getCollection()->addAttributeToSort('price', 'DESC');
                    break;
            }
        }
        return parent::_prepareCollection();
    }

    /**
     * Get product ids by file model
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getProductIds()
    {
        $model = $this->getModel();
        $formRegistry = $this->_coreRegistry->registry('mgz_conditions_form_name');
        $formName = $model->getFormName() ? $model->getFormName() : $formRegistry;
        if ($formName != 'mgz_landing_page_form') {
            $productIds = $this->getFromCache();
            if (!empty($productIds)) {
                return $productIds;
            }
        }

        $storeIds = $model->getStoreId();
        $productIds = [];
        if (!empty($storeIds)) {
            if (in_array(0, $storeIds)) {
                $stores = $this->systemStore->getStoreValuesForForm();
                foreach ($stores as $store) {
                    if (is_array($store['value']) && !empty($store['value'])) {
                        foreach ($store['value'] as $_store) {
                            $store = $this->_storeManager->getStore($_store['value']);
                            $productIds = $this->prosessor->getProductByConditions($model, $store)->getAllIds();
                        }
                    }
                }
            } else {
                foreach ($storeIds as $storeId) {
                    $store = $this->_storeManager->getStore($storeId);
                    $productIds = $this->prosessor->getProductByConditions($model, $store)->getAllIds();
                }
            }
        }
        if ($formName!= 'mgz_landing_page_form') {
            $this->saveToCache($productIds);
        }
        return $productIds;
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Title'),
                'index' => 'name'
            ]
        );

        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'index' => 'sku'
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'header_css_class' => 'col-status data-grid-actions-cell',
                'source' => Status::class,
                'options' => $this->_status->getOptionArray()
            ]
        );

        $this->addColumn(
            'visibility',
            [
                'header' => __('Visibility'),
                'index' => 'visibility',
                'type' => 'options',
                'options' => $this->_visibility->getOptionArray(),
                'header_css_class' => 'col-visibility data-grid-actions-cell',
                'column_css_class' => 'col-visibility'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'mgzcore/conditions/productlist',
            ['current' => true, 'mgz_conditions_grid_id' => $this->getGridId()]
        );
    }

    /**
     * @param  string $key
     * @return string
     */
    public function getGridCacheKey()
    {
        return $this->getGridId();
    }

    /**
     * @return mixed|void
     */
    public function getFromCache()
    {
        if (!$this->getRequest()->getParam('current') || !$this->getGridId()) return;
        if ($this->cacheState->isEnabled(self::CACHE_GROUP)) {
            $key = $this->getGridCacheKey();
            $config = $this->getCacheManager()->load($key);
            if ($config) {
                return $this->coreHelper->unserialize($config);
            }
        }
    }

    /**
     * @param $value
     */
    public function saveToCache($value)
    {
        if ($this->cacheState->isEnabled(self::CACHE_GROUP)) {
            $key = $this->getGridCacheKey();
            $this->getCacheManager()->save(
                $this->coreHelper->serialize($value),
                $key,
                [
                    self::CACHE_TAG
                ]
            );
        }
    }

    /**
     * Retrieve cache interface
     *
     * @return CacheInterface
     * @deprecated 101.0.3
     */
    private function getCacheManager()
    {
        if (!$this->_cacheManager) {
            $this->_cacheManager = ObjectManager::getInstance()
                ->get(CacheInterface::class);
        }
        return $this->_cacheManager;
    }
}
