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

namespace Magezon\CustomerAttachments\Plugin\Model\ResourceModel;

class Attachment
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magezon\CustomerAttachments\Model\EmailFactory
     */
    protected $emailFactory;

    /**
     * @param \Magento\Framework\App\ResourceConnection         $resource           
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository 
     * @param \Magezon\CustomerAttachments\Model\EmailFactory   $emailFactory       
     */
    public function __construct(
    	\Magento\Framework\App\ResourceConnection $resource,
    	\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
    	\Magezon\CustomerAttachments\Model\EmailFactory $emailFactory
    ) {
		$this->_resource          = $resource;
		$this->customerRepository = $customerRepository;
		$this->emailFactory       = $emailFactory;
    }

    public function aroundSave(
    	\Magezon\CustomerAttachments\Model\ResourceModel\Attachment $resourceModel,
    	callable $proceed,
    	\Magezon\CustomerAttachments\Model\Attachment $attachment
    ) {
		$sendEmail = $attachment->getSendEmail();
    	if ($sendEmail && $attachment->getId()) {
    		$oldCustomerIds = $this->getAttachmentCustomerIds($attachment->getId());
    	}

    	$result = $proceed($attachment);

    	if ($sendEmail) {
			$newCustomerIds = $this->getAttachmentCustomerIds($attachment->getId());
			if (!empty($newCustomerIds)) {
				$email = $this->emailFactory->create();
				foreach ($newCustomerIds as $_id) {
					$_customer = $this->customerRepository->getById($_id);
					$email->sendNewAttachment($_customer, [$attachment]);
				}
			}
    	}

    	return $result;
    }

    public function getAttachmentCustomerIds($attachmentId)
    {
		$connection = $this->_resource->getConnection();
		$table      = $this->_resource->getTableName('customerattachments_customer_attachment');
		$select     = $connection->select()
        ->from($table, 'customer_id')
        ->where('attachment_id = ' . $attachmentId);
        return $connection->fetchCol($select);
    }
}
