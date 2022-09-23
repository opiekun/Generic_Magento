<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_Photogallery
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */

namespace FME\Photogallery\Model\ResourceModel\Photogallery;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_storeManager;
    protected $_previewFlag;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_storeManager = $storeManager;
    }

    public function _construct()
    {
        $this->_init('\FME\Photogallery\Model\Photogallery', '\FME\Photogallery\Model\ResourceModel\Photogallery');
        $this->_map['fields']['photogallery_id'] = 'main_table.photogallery_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    public function addPhotogalleryFilter($photogallery)
    {
        if (is_array($photogallery)) {
            $condition = $this->getConnection()->quoteInto('main_table.photogallery_id IN(?)', $photogallery);
        } else {
            $condition = $this->getConnection()->quoteInto('main_table.photogallery_id=?', $photogallery);
        }
        return $this->addFilter('photogallery_id', $condition, 'string');
    }

    public function getPgalleries($productId)
    {
        $this->getSelect()
            ->join(
                ['linked_products' => $this->getTable('photogallery_products')],
                'main_table.photogallery_id = linked_products.photogallery_id',
                []
            )
            ->where('linked_products.product_id = ?', $productId)
            ->where('main_table.show_in = 2 OR main_table.show_in = 3')
            ->where('main_table.status = 1')
            ->order('main_table.gorder', 'ASC');

        return $this;
    }

    public function getPimages($photogalleryIds)
    {
        $this->setConnection($this->getResource()->getConnection());
        $this->getSelect()
            ->from(['g' => $this->getTable('photogallery_images')], '*')
            ->where('g.photogallery_id IN (?)', $photogalleryIds)
            ->order('g.img_order', 'ASC');
        return $this;
    }

    public function addStoreFilter($store)
    {
        if ($store instanceof \Magento\Store\Model\Store) {
            $store = [$store->getId()];
        }
        $this->getSelect()
            ->join(
                ['store_table' => $this->getTable('photogallery_store')],
                'main_table.photogallery_id = store_table.photogallery_id',
                []
            )
            ->where('store_table.store_id in (?)', [0, $store])
            ->distinct(true);
        ;
        return $this;
    }

    protected function performAfterLoad($tableName, $columnName)
    {
        $items = $this->getColumnValues($columnName);
        if (count($items)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['store_table' => $this->getTable($tableName)])
                ->where('store_table.' . $columnName . ' IN (?)', $items);
            $result = $connection->fetchPairs($select);
            if ($result) {
                foreach ($this as $item) {
                    $entityId = $item->getData($columnName);
                    if (!isset($result[$entityId])) {
                        continue;
                    }
                    if ($result[$entityId] == 0) {
                        $stores = $this->_storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                        $storeCode = key($stores);
                    } else {
                        $storeId = $result[$item->getData($columnName)];
                        $storeCode = $this->_storeManager->getStore($storeId)->getCode();
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_code', $storeCode);
                    $item->setData('store_id', [$result[$entityId]]);
                }
            }
        }
    }

    protected function _afterLoad()
    {
        $this->performAfterLoad('photogallery_store', 'photogallery_id');
        $this->_previewFlag = false;
        return parent::_afterLoad();
    }
}
