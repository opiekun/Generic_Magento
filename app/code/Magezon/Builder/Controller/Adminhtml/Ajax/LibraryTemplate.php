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

namespace Magezon\Builder\Controller\Adminhtml\Ajax;

class LibraryTemplate extends \Magento\Backend\App\Action
{
    /**
     * @var \Magezon\Builder\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param \Magento\Backend\App\Action\Context $context    
     * @param \Magezon\Builder\Helper\Data        $dataHelper 
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magezon\Builder\Helper\Data $dataHelper
    ) {
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
    }

    public function execute()
    {
        $result = [];
        $post   = $this->getRequest()->getPostValue();
        if (isset($post['url']) && $post['url']) {
            $result = $this->dataHelper->getTemplates($post['url']);
        }
    	$this->getResponse()->representJson(
            $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result)
        );
    }
}