<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://magezon.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_CustomerAttachments
 * @copyright Copyright (C) 2018 Magezon (https://magezon.com)
 */

namespace Magezon\CustomerAttachments\Block\Adminhtml\Renderer;
use Magento\Framework\UrlInterface;

class CustomerAction extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
	/**
	 * @var Magento\Framework\UrlInterface
	 */
	protected $_urlBuilder;

	/**
	 * @param \Magento\Backend\Block\Context
	 * @param UrlInterface
	 */
	public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\Url $urlBuilder
    ){
		$this->_urlBuilder = $urlBuilder;
        parent::__construct($context);
	}

	public function _getValue(\Magento\Framework\DataObject $row){
		$customerEditUrl = $this->_urlBuilder->getUrl(
            'customer/index/edit',
            [
                'id' => $row['entity_id']
            ]
        );
		return sprintf("<a href='%s' target='_blank'>Edit</a>", $customerEditUrl);
	}
}
