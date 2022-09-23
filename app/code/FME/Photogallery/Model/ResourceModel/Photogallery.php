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

namespace FME\Photogallery\Model\ResourceModel;

class Photogallery extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $_store = null;
    protected $_date;
    protected $_storeManager;
    protected $dateTime;
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->_objectManager = $objectManager;
    }
    
    public function _construct()
    {
        $this->_init('photogallery', 'photogallery_id');
    }
    
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {        
        $select = $this->getConnection()->select()
            ->from($this->getTable('photogallery_store'))
            ->where('photogallery_id = ?', $object->getId());
        if ($data = $this->getConnection()->fetchAll($select)) {
            $storesArray = [];
            foreach ($data as $row) {
                $storesArray[] = $row['store_id'];
            }
            $object->setData('store_id', $storesArray);
        }
        $select = $this->getConnection()->select()
            ->from($this->getTable('photogallery_products'))
            ->where('photogallery_id = ?', $object->getId());
        if ($data = $this->getConnection()->fetchAll($select)) {
            $productsArray = [];
            foreach ($data as $row) {
                $productsArray[] = $row['product_id'];
            }
            $object->setData('product_id', $productsArray);
        }
        $category_ids = $object->getData("category_ids");
        if ($category_ids != "") {
            $object->setData("photogallery_categories", $category_ids);
        }
        return parent::_afterLoad($object);
    }

    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = $this->getConnection()->quoteInto('photogallery_id = ?', $object->getId());
         $links = $object->getData("product_id");
        if (isset($links)) {
            $productIds = $links;
            $this->getConnection()->delete($this->getTable('photogallery_products'), $condition);
            foreach ($productIds as $_product) {
                $newsArray = [];
                $newsArray['photogallery_id'] = $object->getId();
                $newsArray['product_id'] = $_product;
                $this->getConnection()->insert($this->getTable('photogallery_products'), $newsArray);
            }
        }
        $stores = $object->getData("store_id");
        if (isset($stores)) {
             $this->getConnection()->delete($this->getTable('photogallery_store'), $condition);
            foreach ($stores as $store) {
                $storeArray = [];
                $storeArray['photogallery_id'] = $object->getId();
                $storeArray['store_id'] = $store;
                $this->getConnection()->insert($this->getTable('photogallery_store'), $storeArray);
            }
        }
        return parent::_afterSave($object);
    }
}
