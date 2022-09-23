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

namespace Magezon\CustomerAttachments\Controller\File;

use Magento\Framework\App\ResponseInterface;
use Magezon\CustomerAttachments\Model\Attachment;

class Download extends \Magezon\CustomerAttachments\Controller\Download
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magezon\CustomerAttachments\Helper\File
     */
    protected $fileHelper;

    /**
     * @var \Magezon\CustomerAttachments\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param \Magento\Framework\App\Action\Context    $context         
     * @param \Magento\Customer\Model\Session          $customerSession 
     * @param \Magezon\CustomerAttachments\Helper\File $fileHelper      
     * @param \Magezon\CustomerAttachments\Helper\Data $dataHelper      
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magezon\CustomerAttachments\Helper\File $fileHelper,
        \Magezon\CustomerAttachments\Helper\Data $dataHelper
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->fileHelper      = $fileHelper;
        $this->dataHelper      = $dataHelper;
    }

    /**
     * Download file action
     *
     * @return void|ResponseInterface
     */
    public function execute()
    {
        if (!$this->customerSession->isLoggedIn()) {
            return $this->_redirect('customer/account');
        }
        $route      = $this->dataHelper->getRoute();
        $customerId = $this->customerSession->getCustomerId();
        $id         = $this->getRequest()->getParam('id', 0);
        $collection = $this->_objectManager->create(
            \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\Collection::class
        );

        $attachment = $collection->addWebsiteFilter()
        ->addCustomersFilter($customerId)
        ->addDateToFilter()
        ->addIsActiveFilter()
        ->addFieldToFilter('attachment_hash', $id)
        ->getFirstItem();
        if (!$attachment->getId()) {
            $this->messageManager->addNotice(__("We can't find the file you requested."));
            return $this->_redirect($route);
        }

        $numberOfDownloadsUsed = $this->fileHelper->getNumberOfDownloadsUsed($attachment->getId(), $customerId);
        $downloadsLeft         = $attachment->getNumberOfDownloads() - $numberOfDownloadsUsed;

        if (($downloadsLeft>0) || ($attachment->getNumberOfDownloads() == 0)) {
            $resource = $resourceType = '';
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
                $this->fileHelper->saveReportDownload($attachment->getId(), $customerId, $numberOfDownloadsUsed+1);
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong while getting the requested content.'));
            }
        } else {
            $this->messageManager->addNotice(__('The file has expired.'));
        }
        return $this->_redirect($route);
    }
}

