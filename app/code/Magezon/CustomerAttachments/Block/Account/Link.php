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

use Magento\Customer\Block\Account\SortLinkInterface;

class Link extends \Magento\Framework\View\Element\Html\Link implements SortLinkInterface
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
     * @param \Magezon\CustomerAttachments\Helper\Data         $helperData 
     * @param \Magezon\CustomerAttachments\Helper\File         $fileHelper 
     * @param array                                            $data       
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magezon\CustomerAttachments\Helper\Data $helperData,
        \Magezon\CustomerAttachments\Helper\File $fileHelper,
        array $data = []
    ) {
        $this->helperData = $helperData;
        $this->fileHelper = $fileHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if (!$this->helperData->getConfig('general/show_toplink')) {
            return;
        }
        if ($this->helperData->getConfig('general/hide_without_file')) {
            if (!$this->fileHelper->isHideTitle()) {
                return;
            }
        }
        return parent::toHtml();
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl($this->helperData->getRoute());
    }

    /**
     * {@inheritdoc}
     * @since 100.2.0
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->helperData->getTitle();
    }
}
