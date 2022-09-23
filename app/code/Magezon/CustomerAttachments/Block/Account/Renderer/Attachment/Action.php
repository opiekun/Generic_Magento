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

namespace Magezon\CustomerAttachments\Block\Account\Renderer\Attachment;

use Magento\Framework\UrlInterface;

class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
	/**
	 * @var Magento\Framework\UrlInterface
	 */
	protected $_urlBuilder;

	/**
	 * @var \Magezon\CustomerAttachments\Helper\Data
	 */
	protected $helperdata;

	/**
	 * @param \Magento\Backend\Block\Context           $context    
	 * @param \Magento\Framework\UrlInterface          $urlBuilder 
	 * @param \Magezon\CustomerAttachments\Helper\Data $helperdata 
	 */
	public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magezon\CustomerAttachments\Helper\Data $helperdata
    ) {
		$this->_urlBuilder = $urlBuilder;
		$this->helperdata  = $helperdata;
        parent::__construct($context);
	}

	public function _getValue(\Magento\Framework\DataObject $row)
	{
		$numberOfDownloadsUsed = $row->getNumberOfDownloadsUsed();
		$downloadsLeft         = $row->getNumberOfDownloads() - $numberOfDownloadsUsed;
		$key                   = $row->getAttachmentKey();

		if (!$key) {
			return '---';
		}

		if ($downloadsLeft <= 0 && $row->getNumberOfDownloads() > 0) {
			return __('Expired');
		}

		$route   = $this->helperdata->getRoute();
		$editUrl = $this->_urlBuilder->getUrl($route . '/file/download', ['id' => $key]);
		return sprintf("<a href='%s' class='mca-download action download'><span>Download</span></a>", $editUrl);
	}
}
