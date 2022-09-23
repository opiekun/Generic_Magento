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

namespace Magezon\CustomerAttachments\Helper;

use Magento\Framework\UrlInterface;
use Magezon\CustomerAttachments\Model\Attachment;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ROUTE = 'mcafiles';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context      $context      
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager 
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
    }
   
    /**
     * @param  string $key
     * @param  null|int $store
     * @return null|string
     */
    public function getConfig($key, $store = null)
    {
        $store     = $this->_storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();
        $result    = $this->scopeConfig->getValue(
            'customerattachments/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }

    /**
     * @return boolean
     */
    public function isEnable()
    {
        return $this->getConfig('general/enable');
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        $route = $this->getConfig('general/route');
        if (!$route) {
            $route = self::ROUTE;
        }
        return $route;   
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        $title = $this->getConfig('general/title');
        if (!$title) {
            $title == __('My Files');
        }
        return $title;   
    }	
}
