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
namespace FME\Photogallery\Model;

class Photogallery extends \Magento\Framework\Model\AbstractModel
{

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;
    protected $_objectManager;
    protected $_coreResource;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ResourceConnection $coreResource,
        \FME\Photogallery\Model\ResourceModel\Photogallery $resource,
        \FME\Photogallery\Model\ResourceModel\Photogallery\Collection $resourceCollection
    ) {
        $this->_objectManager = $objectManager;
        $this->_coreResource = $coreResource;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }

    public function _construct()
    {
        $this->_init('FME\Photogallery\Model\ResourceModel\Photogallery');
    }
    public function getRelatedProducts($attachmentId)
    {
        $photogalleryTable = $this->_coreResource
            ->getTableName('photogallery_products');

        $collection = $this->_objectManager->create('FME\Photogallery\Model\Photogallery')
            ->getCollection()
            ->addFieldToFilter('main_table.photogallery_id', $attachmentId);
        $collection->getSelect()
            ->joinLeft(
                ['related' => $photogalleryTable],
                'main_table.photogallery_id = related.photogallery_id'
            )
            ->order('main_table.photogallery_id');
        return $collection->getData();
    }

    public function getProducts(\FME\Photogallery\Model\Photogallery $object)
    {
        $select = $this->_getResource()->getConnection()->select()
            ->from($this->_getResource()->getTable('photogallery_products'))
            ->where('photogallery_id = ?', $object->getId());
        $data = $this->_getResource()->getConnection()
        ->fetchAll($select);
        if ($data) {
            $productsArr = [];
            foreach ($data as $_i) {
                $productsArr[] = $_i['product_id'];
            }
            return $productsArr;
        }
    }

    public function checkPhotogallery($id)
    {
        return $this->_getResource()->checkPhotogallery($id);
    }

    public function deletePhotogalleryStores($id)
    {
        return $this->getResource()->deletePhotogalleryStores($id);
    }
    public function deletePhotogalleryProductLinks($id)
    {
        return $this->getResource()->deletePhotogalleryProductLinks($id);
    }

    public function checkIdentifier($identifier)
    {
        return $this->_getResource()->checkIdentifier($identifier);
    }

    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    public function getProductsPosition()
    {
        if (!$this->getId()) {
            return [];
        }
        $array = $this->getData('products_position');
        if ($array === null) {
            $temp = $this->getData('product_id');
            if($this->getData('product_id')>-1)
            {
                for ($i = 0; $i < sizeof($this->getData('product_id')); $i++) {
                    $array[$temp[$i]] = 0;
                }
            }
            $this->setData('products_position', $array);
        }
        return $array;
    }
}
