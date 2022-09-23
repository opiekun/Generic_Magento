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

namespace Magezon\CustomerAttachments\Controller\Index;

class Grid extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \Magento\Framework\App\Action\Context           $context             
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory    
     * @param \Magento\Framework\View\Result\LayoutFactory    $resultLayoutFactory 
     * @param \Magento\Customer\Model\Session                 $customerSession     
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Customer\Model\Session $customerSession
    ) {
        parent::__construct($context);
        $this->resultRawFactory    = $resultRawFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->customerSession     = $customerSession;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setUrl($this->_url->getUrl('customer/account'));
            return $resultRedirect;
        }
        if (!$this->customerSession->isLoggedIn()) {
            $result = [
                'ajaxExpired'  => 1,
                'ajaxRedirect' => $this->_url->getUrl('customer/account')
            ];
            return $this->getResponse()->representJson(
                $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result)
            );
        }

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw    = $this->resultRawFactory->create();
        $resultLayout = $this->resultLayoutFactory->create();
        return $resultRaw->setContents(
            $resultLayout->getLayout()->createBlock(
                \Magezon\CustomerAttachments\Block\Account\Attachments::class,
                'customer.attachments'
            )->toHtml()
        );
    }
}
