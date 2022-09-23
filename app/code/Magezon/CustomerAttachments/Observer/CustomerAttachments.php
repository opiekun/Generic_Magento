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

namespace Magezon\CustomerAttachments\Observer;

use Magezon\CustomerAttachments\Model\Attachment;

class CustomerAttachments implements \Magento\Framework\Event\ObserverInterface
{
	/**
	 * @var \Magento\Framework\App\ResourceConnection
	 */
	protected $_resource;

	/**
	 * @var \Magento\Framework\Json\DecoderInterface
	 */
	protected $_jsonDecoder;

	/**
	 * @var \Magezon\CustomerAttachments\Helper\File
	 */
	protected $fileHelper;

    /**
     * @var \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\CollectionFactory
     */
    protected $attachmentCollectionFactory;

    /**
     * @var \Magezon\CustomerAttachments\Model\EmailFactory
     */
    protected $emailFactory;

    /**
     * Id of next attachment entity row
     *
     * @var int
     */
    protected $_nextAttachmentId;

    /**
     * @param \Magento\Framework\App\ResourceConnection                                     $resource                    
     * @param \Magento\Framework\Json\DecoderInterface                                      $jsonDecoder                 
     * @param \Magento\Store\Model\StoreManagerInterface                                    $storeManager                
     * @param \Magezon\CustomerAttachments\Helper\File                                      $fileHelper                  
     * @param \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollectionFactory 
     * @param \Magezon\CustomerAttachments\Model\EmailFactory                               $emailFactory                
     */
    public function __construct(
    	\Magento\Framework\App\ResourceConnection $resource,
    	\Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
    	\Magezon\CustomerAttachments\Helper\File $fileHelper,
        \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollectionFactory,
        \Magezon\CustomerAttachments\Model\EmailFactory $emailFactory
    ) {
        $this->_resource                   = $resource;
        $this->_jsonDecoder                = $jsonDecoder;
        $this->storeManager                = $storeManager;
        $this->fileHelper                  = $fileHelper;
        $this->attachmentCollectionFactory = $attachmentCollectionFactory;
        $this->emailFactory                = $emailFactory;
    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    public function getConnection()
    {
    	return $this->_resource->getConnection();
    }

    public function getTableName($tableName)
    {
    	return $this->_resource->getTableName($tableName);
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		$request      = $observer->getRequest();
		$customer     = $observer->getCustomer();
		$customerData = $request->getParam('customer');
		$customerId   = $customer->getId();
		$connection   = $this->getConnection();

    	if (isset($customerData['customer_attachments']) && $customerData['customer_attachments']) {
    		parse_str($customerData['customer_attachments'], $customerAttachments);
			$cond = ['customer_id=?' => $customerId];
    		$connection->delete($this->getAttachmentCustomerTable(), $cond);
            $attachments = $websites = $attachmentFiles = $entitiesToDelete = $entitiesToCreate = $entitiesToUpdate = [];
            $newIds      = [];
            $i           = 0;

            $defaultWebsiteId = $this->storeManager->getWebsite()->getId();
    		foreach ($customerAttachments['attachments'] as $_attachment) {
    			if (isset($_attachment['is_delete']) && $_attachment['is_delete'] && isset($_attachment['attachment_id'])) {
    				$entitiesToDelete[] = $_attachment['attachment_id'];
    				continue;
    			}

    			$attachmentFile = $this->_jsonDecoder->decode($_attachment['attachment_file']);
    			if (is_array($attachmentFile)) {
    				if (!empty($attachmentFile) && count($attachmentFile)>0) {
    					$attachmentFiles[] = $attachmentFile = str_replace('.tmp', '', $attachmentFile[0]['file']);
    				} else {
    					$attachmentFile = '';
    				}
    			}
    			$_attachment['attachment_file'] = $attachmentFile;
                if (!$_attachment['attachment_hash']) {
                    $_attachment['attachment_hash'] = $this->fileHelper->getFileHash();
                }

    			unset($_attachment['is_delete']);
                unset($_attachment['sort_order']);
    			if (!isset($_attachment['attachment_id']) || !$_attachment['attachment_id']) {
    				unset($_attachment['attachment_id']);
    				$_id = $newIds[] = $this->_getAttachmentId();
    				$websites[] = [
                        'attachment_id' => $_id,
                        'website_id'    => $defaultWebsiteId
    				];
                    $_attachment['number_of_downloads'] = 0;
    				$entitiesToCreate[] = $_attachment;
    			} else {
					$entitiesToUpdate[] = $_attachment;
					$_id                = $_attachment['attachment_id'];
    			}
    			$attachments[] = [
    				'customer_id'   => $customerId,
    				'attachment_id' => $_id,
    				'position'      => $i,
    				'type'          => Attachment::TYPE_FIXED
    			];
    			$i++;
    		}

    		$table = $this->getTableName('customerattachments_attachment');
    		if (!empty($entitiesToCreate)) {
    			$connection->insertMultiple(
    				$table,
    				$entitiesToCreate
    			);
    			$connection->insertMultiple($this->getTableName('customerattachments_attachment_website'), $websites);

                $collection = $this->attachmentCollectionFactory->create();
                $collection->addFieldToFilter('attachment_id', ['in' => $newIds]);
                $email = $this->emailFactory->create();
                $email->sendNewAttachment($customer, $collection->getItems());
    		}

    		if (!empty($entitiesToUpdate)) {
                foreach ($entitiesToUpdate as $_row) {
                    $connection->update($table, $_row, ['attachment_id = ?' => $_row['attachment_id']]);
                }
    		}

    		if (!empty($entitiesToDelete)) {
				$cond = [
    				'attachment_id IN (?)' => $entitiesToDelete,
    				'customer_id = ?' => $customerId
    			];
    			$connection->delete($this->getAttachmentCustomerTable(), $cond);
    		}

    		if (!empty($attachments)) {
    			$connection->insertMultiple($this->getAttachmentCustomerTable(), $attachments);
    		}

    		if (!empty($attachmentFiles)) {
    			foreach ($attachmentFiles as $_file) {
    				$this->fileHelper->moveFileFromTmp($_file);
    			}
    		}
    	}
    }

    /**
     * Attachment customer table name getter
     *
     * @return string
     */
    public function getAttachmentCustomerTable()
    {
    	return $this->getTableName('customerattachments_customer_attachment');
    }

    /**
     * Retrieve next attachment id
     *
     * @return int
     */
    protected function _getAttachmentId()
    {
    	if (!$this->_nextAttachmentId) {
			$connection   = $this->getConnection();
			$entityStatus = $connection->showTableStatus($this->getTableName('customerattachments_attachment'));
    		$this->_nextAttachmentId = $entityStatus['Auto_increment'];
    	}
    	return $this->_nextAttachmentId++;
    }
}
