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
 * @package   Magezon_Builder
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Builder\Model\Source;

class ProductList implements \Magezon\Builder\Model\Source\ListInterface
{
	/**
	 * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
	 */
	protected $collectionFactory;

	/**
	 * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
	 */
	public function __construct(
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
	) {
		$this->collectionFactory = $collectionFactory;
	}

	public function getItem($q) {
		$data = [];
		$collection = $this->collectionFactory->create();
		$collection->addFieldToSelect('name');
		if (is_numeric($q)) {
			$collection->addAttributeToFilter('entity_id', $q);
		} else {
			$collection->addAttributeToFilter('sku', $q);
		}
		$item = $collection->getFirstItem();
		if ($item->getId()) {
			$data = [
				'label' => $item->getName(),
				'value' => $item->getId()
			];
		}
		return $data;
	}

	public function getList($q = '', $field = '') {

		$list = [];
		$collection = $this->collectionFactory->create();
		$collection->addFieldToSelect('name');
		$collection->setOrder('name', 'ASC');

		if ($q) {
			if (is_array($q)) {
				$collection->addAttributeToFilter('entity_id', ['in' => $q]);
			} else if (is_numeric($q)) {
	            $collection->addAttributeToFilter('entity_id', $q);
	        } else {
	            $collection->addAttributeToFilter([
	            	[
						'attribute' => 'name',
						'like'      => '%' . $q . '%'
	            	],
	            	[
						'attribute' => 'sku',
						'like'      => '%' . $q . '%'
	            	]
	            ]);
	        }
	    }
		foreach ($collection as $item) {
			$value = $item->getId();
			if ($field == 'product_sku') {
				$value = $item->getSku();
			}
            $list[] = [
				'label' => $item->getName(),
				'value' => $value
            ];
        }
        return $list;
	}
}