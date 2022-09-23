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

namespace Magezon\CustomerAttachments\Block\Account;

class SortLink extends \Magento\Framework\View\Element\Html\Link\Current implements \Magento\Customer\Block\Account\SortLinkInterface
{
	/**
	 * @var \Magezon\CustomerAttachments\Helper\Data
	 */
	protected $helperData;

    /**
     * @var \Magezon\CustomerAttachments\Helper\File
     */
    protected $fileHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context     
     * @param \Magento\Framework\App\DefaultPathInterface      $defaultPath 
     * @param \Magezon\CustomerAttachments\Helper\Data         $helperData  
     * @param \Magezon\CustomerAttachments\Helper\File         $fileHelper  
     * @param array                                            $data        
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magezon\CustomerAttachments\Helper\Data $helperData,
        \Magezon\CustomerAttachments\Helper\File $fileHelper,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath);
        $this->helperData = $helperData;
        $this->fileHelper = $fileHelper;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if ($this->helperData->getConfig('general/hide_without_file')) {
            if (!$this->fileHelper->isHideTitle()) {
                return;
            }
        }
        return parent::toHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * Get href URL
     *
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl($this->helperData->getRoute());
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->helperData->getTitle();
    }
}
