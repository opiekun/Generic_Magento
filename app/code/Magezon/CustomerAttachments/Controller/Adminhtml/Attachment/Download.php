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

use Magezon\CustomerAttachments\Model\Attachment;

class Download extends \Magezon\CustomerAttachments\Controller\Download
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magezon_CustomerAttachments::attachment';

    /**
     * @var \Magezon\CustomerAttachments\Helper\File
     */
    protected $fileHelper;

    /**
     * @param \Magento\Backend\App\Action\Context      $context    
     * @param \Magezon\CustomerAttachments\Helper\File $fileHelper 
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magezon\CustomerAttachments\Helper\File $fileHelper
    ) {
        parent::__construct($context);
        $this->fileHelper = $fileHelper;
    }

    /**
     * Download process
     *
     * @param string $resource
     * @param string $resourceType
     * @return void
     */
    public function execute()
    {
    	// 1. Get ID and create attachment
        $id         = $this->getRequest()->getParam('attachment_id');
        $attachment = $this->_objectManager->create(Attachment::class);
        $attachment->load($id);

        if ($attachment->getId()) {
            if ($attachment->getAttachmentType() == Attachment::FILE_TYPE_URL) {
                $resource     = $attachment->getAttachmentUrl();
                $resourceType = Attachment::FILE_TYPE_URL;
            } else if ($attachment->getAttachmentType() == Attachment::FILE_TYPE_FILE) {
                $resourceType = Attachment::FILE_TYPE_FILE;
                $resource     = $this->fileHelper->getFilePath(
                    $this->fileHelper->getBaseMediaPath(),
                    $attachment->getAttachmentFile()
                );
            }
            try {
                $this->_processDownload($resource, $resourceType);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError(__('Something went wrong while getting the requested content.'));
            }
        } else {
            $this->messageManager->addError(__('This attachment no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }
    }
}
