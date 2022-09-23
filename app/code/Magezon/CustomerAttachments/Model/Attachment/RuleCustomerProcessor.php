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

namespace Magezon\CustomerAttachments\Model\Attachment;

use Magento\Customer\Model\ResourceModel\Customer\Collection;
use Magezon\CustomerAttachments\Model\Attachment;
use \Magento\Sales\Model\Order;

class RuleCustomerProcessor extends \Magento\Framework\DataObject
{
	/**
	 * @var null|array
	 */
	private $_reports = null;

	/**
	 * @var null|array
	 */
	private $attachments = null;

	/**
	 * @var boolean
	 */
	private $_sendMail = true;

	/**
	 * @var \Magento\Framework\App\ResourceConnection
	 */
	protected $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
	protected $connection;

	/**
	 * @var \Magento\Rule\Model\Condition\Sql\Builder
	 */
	protected $sqlBuilder;

	/**
	 * @var \Magento\Framework\ObjectManagerInterface
	 */
	protected $_objectManager;

	/**
	 * @var \Magento\Customer\Api\CustomerRepositoryInterface
	 */
	protected $customerRepository;

	/**
	 * @var \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\CollectionFactory
	 */
	protected $attachmentCollectionFactory;

	/**
	 * @var \Magezon\CustomerAttachments\Model\EmailFactory
	 */
	protected $emailFactory;

	/**
	 * @param \Magento\Framework\App\ResourceConnection                                     $resource                    
	 * @param \Magezon\CustomerAttachments\Model\Attachment\Condition\Sql\Builder           $sqlBuilder                  
	 * @param \Magento\Framework\ObjectManagerInterface                                     $objectManager               
	 * @param \Magento\Customer\Api\CustomerRepositoryInterface                             $customerRepository          
	 * @param \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollectionFactory 
	 * @param \Magezon\CustomerAttachments\Model\EmailFactory                               $emailFactory                
	 */
	public function __construct(
		\Magento\Framework\App\ResourceConnection $resource,
		\Magezon\CustomerAttachments\Model\Attachment\Condition\Sql\Builder $sqlBuilder,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
		\Magezon\CustomerAttachments\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollectionFactory,
		\Magezon\CustomerAttachments\Model\EmailFactory $emailFactory
	) {
		$this->resource                    = $resource;
		$this->connection                  = $resource->getConnection();
		$this->sqlBuilder                  = $sqlBuilder;
		$this->_objectManager              = $objectManager;
		$this->customerRepository          = $customerRepository;
		$this->attachmentCollectionFactory = $attachmentCollectionFactory;
		$this->emailFactory                = $emailFactory;
	}

	public function applyAllRules()
	{
		try {
			$this->updateRulesCustomer();
			$this->setSuccess(__('Updated rules applied.'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
        }
	}

    protected function getCustomerAttachmentTable()
    {
    	return $this->resource->getTableName('customerattachments_customer_attachment');
    }

	/**
	 * @param  int $id
	 * @return void
	 */
	public function applyById($id)
	{
		$this->updateRulesCustomer([$id]);
	}

	/**
	 * @param  int $attachmentId
	 * @param  int $customerId
	 * @return int
	 */
    public function getNumberOfDownloadsUsed($attachmentId, $customerId)
    {
    	if ($this->_reports == null) {
			$reportTable    = $this->resource->getTableName('customerattachments_customer_attachment_report');
			$select         = $this->connection->select()->from($reportTable);
			$this->_reports = $this->connection->fetchAll($select);
		}
		$count = 0;
		if ($this->_reports) {
			foreach ($this->_reports as $_row) {
				if ($_row['attachment_id'] == $attachmentId && $_row['customer_id'] == $customerId) {
					$count++;
				}
			}
		}
        return $count;
    }

    /**
     * @return array|null
     */
    public function getCustomerAttachments()
    {
    	if ($this->attachments == null) {
			$select = $this->connection->select()
			->from($this->getCustomerAttachmentTable())
			->order('position ASC');
			$this->attachments = $this->connection->fetchAll($select);
		}
		return $this->attachments;
    }

    /**
     * @param  int $attachmentId
     * @return int
     */
    public function getAttachmentCustomerIds($attachmentId)
    {
    	$ids = [];
    	$attachments = $this->getCustomerAttachments();
    	foreach ($attachments as $_row) {
    		if ($_row['attachment_id'] == $attachmentId) {
    			$ids[] = $_row['customer_id'];
    		}
    	}
    	return $ids;
    }

    protected function prepareCollection($collection, $_attachment)
    {
    	$conditions = $_attachment->getConditions()->asArray();
    	if (isset($conditions['conditions'])) {
	    	$conditions = $conditions['conditions'];

	    	$joinSalesOrder = $joinNewsletterSubscriber = false;
	    	foreach ($conditions as $_condition) {
	    		if ($_condition['attribute'] == 'orders_number' || $_condition['attribute'] == 'orders_sum') {
	    			$joinSalesOrder = true;
	    		}
	    		if ($_condition['attribute'] == 'is_subscriber') {
	    			$joinNewsletterSubscriber = true;
	    		}
	    	}

	    	if ($joinSalesOrder) {
		    	$collection->getSelect()->joinLeft(
		            ['mgzso' => $collection->getResource()->getTable('sales_order')],
		            'e.entity_id = mgzso.customer_id AND mgzso.status = "' . Order::STATE_COMPLETE . '"',
		            [
		            	'orders_number' => 'COUNT(mgzso.customer_id)',
		            	'orders_sum' => 'SUM(mgzso.grand_total)'
		            ]
		        )->group('e.entity_id');
		    }

		    if ($joinNewsletterSubscriber) {
		    	$collection->getSelect()->joinLeft(
		            ['mgzns' => $collection->getResource()->getTable('newsletter_subscriber')],
		            'e.entity_id = mgzns.customer_id',
		            ['is_subscriber' => 'subscriber_status']
		        );
		    }

		    if ($joinSalesOrder || $joinNewsletterSubscriber) {
		    	return true;
		    }
	        return false;
	    }
	    return true;
    }

    /**
     * @param  array $ids
     * @return void
     */
    protected function updateRulesCustomer($ids = [])
    {
    	$collection = $this->attachmentCollectionFactory->create();
    	$collection->addDateToFilter()
		->addIsActiveFilter()
		->addFieldToFilter('attachment_hash', ['neq' => 'NULL'])
		->setOrder('attachment_id', 'DESC');
    	if (!empty($ids)) {
    		$collection->addFieldToFilter('attachment_id', ['in' => $ids]);
    	}

    	$attachments = [];
    	foreach ($collection as $_attachment) {
    		if ($_attachment->getEnableCondition()) {
				$customerCollection = $this->_objectManager->create(Collection::class);
				$conditionStatus    = $this->prepareCollection($customerCollection, $_attachment);
				$where              = $this->sqlBuilder->attachConditionToCollection($customerCollection, $_attachment->getConditions());
				if (!empty($where)) {
					if ($conditionStatus) {
						$customerCollection->getSelect()->having($where);
					} else {
						$customerCollection->getSelect()->where($where);
					}
				}

				$oldCustomers = $this->getAttachmentCustomerIds($_attachment->getId());
				$customers    = [];
				foreach ($customerCollection as $_customer) {
					$customers[] = $_customer->getId();
				}

				$insert = array_diff($customers, $oldCustomers);
				$delete = array_diff($oldCustomers, $customers);

				/**
		         * Delete customers from attachment
		         */
		        if (!empty($delete)) {
		            $cond = [
		                'customer_id IN(?)' => $delete,
		                'attachment_id=?' => $_attachment->getId(),
		                'type=?' => Attachment::TYPE_AUTO
		            ];
		            $this->connection->delete($this->getCustomerAttachmentTable(), $cond);
		        }

		        /**
		         * Add customers to attachment
		         */
		        if (!empty($insert)) {
	    			$websiteIds = $_attachment->getData('website_id');
					$data = [];
		        	foreach ($customerCollection as $_customer) {
		        		if (!in_array($_customer->getWebsiteId(), $websiteIds)) continue;
		        		if (!in_array($_customer->getId(), $insert)) continue;
		        		$data[] = [
							'attachment_id'            => $_attachment->getId(),
							'customer_id'              => $_customer->getId(),
							'type'                     => Attachment::TYPE_AUTO,
							'position'                 => 0,
							'number_of_downloads_used' => $this->getNumberOfDownloadsUsed($_attachment->getId(), $_customer->getId())
						];
						$attachments[$_customer->getId()]['customer']      = $_customer->getId();
						$attachments[$_customer->getId()]['attachments'][] = $_attachment;
		        	}
		        	if (!empty($data)) {
						$this->connection->insertMultiple($this->getCustomerAttachmentTable(), $data);
					}
		        }
		    } else {
		    	$cond = [
	                'attachment_id=?' => $_attachment->getId(),
	                'type=?' => Attachment::TYPE_AUTO
	            ];
	            $this->connection->delete($this->getCustomerAttachmentTable(), $cond);
		    }
    	}

    	if ($this->_sendMail && !empty($attachments)) {
    		$email = $this->emailFactory->create();
    		foreach ($attachments as $_attachments) {
				$_customer = $this->customerRepository->getById($_attachments['customer']);
				$email->sendNewAttachment($_customer, $_attachments['attachments']);
    		}
    	}
    }
}
