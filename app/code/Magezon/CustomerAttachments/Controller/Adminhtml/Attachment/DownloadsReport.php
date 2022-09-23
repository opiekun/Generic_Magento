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

namespace Magezon\CustomerAttachments\Controller\Adminhtml\Attachment;

class DownloadsReport extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\App\Action\Context             $context          
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory 
     * @param \Magento\Framework\View\LayoutFactory           $layoutFactory    
     * @param \Magento\Framework\Registry                     $registry         
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory    = $layoutFactory;
        $this->_coreRegistry    = $registry;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id    = $this->getRequest()->getParam('attachment_id');
        $model = $this->_objectManager->create(\Magezon\CustomerAttachments\Model\Attachment::class);
        $model->load($id);
        if (!$model->getId()) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('customerattachments/*/', ['_current' => true, 'id' => null]);
        }

        $this->_coreRegistry->register('customerattachments_attachment', $model);

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                \Magezon\CustomerAttachments\Block\Adminhtml\Attachment\Edit\Tab\Downloads::class,
                'attachment.downloadsreport.grid'
            )->setAttachment($model)->toHtml()
        );
    }
}
