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

class PageList implements \Magezon\Builder\Model\Source\ListInterface
{
	/**
	 * @var \Magento\Cms\Model\PageFactory
	 */
	protected $pageFactory;

	/**
	 * @var \Magento\Cms\Model\ResourceModel\Page\CollectionFactory
	 */
	protected $collectionFactory;

	/**
	 * @param \Magento\Cms\Model\PageFactory                          $pageFactory   
	 * @param \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $collectionFactory 
	 */
	public function __construct(
		\Magento\Cms\Model\PageFactory $pageFactory,
		\Magento\Cms\Model\ResourceModel\Page\CollectionFactory $collectionFactory
	) {
		$this->pageFactory       = $pageFactory;
		$this->collectionFactory = $collectionFactory;
	}

	public function getItem($id) {
		$data = [];
		$page = $this->pageFactory->create();
		$page->load($id);
		if ($page->getId()) {
			$data = [
				'label' => $page->getTitle(),
				'value' => $page->getId()
			];
		}
		return $data;
	}

	public function getList($q = '', $field = '') {
		$list = [];
		$collection = $this->collectionFactory->create();
		$collection->setOrder('title', 'ASC');
		if ($q) {
			if (is_array($q)) {
				$collection->addFieldToFilter('page_id', ['in' => $q]);
			} else if (is_numeric($q)) {
	            $collection->addFieldToFilter('page_id', $q);
	        } else {
				$collection->addFieldToFilter('title', ['like' => '%' . $q . '%']);
	        }
	    }
		foreach ($collection as $item) {
            $list[] = [
				'label' => $item->getTitle(),
				'value' => $item->getId()
            ];
        }
        return $list;
	}
}