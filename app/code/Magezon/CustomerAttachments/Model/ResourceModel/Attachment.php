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

namespace Magezon\CustomerAttachments\Model\ResourceModel;

use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\EntityManager\EntityManager;
use Magezon\CustomerAttachments\Api\Data\AttachmentInterface;
use Magento\Framework\Event\ManagerInterface;
use Magezon\CustomerAttachments\Model\Attachment as AttachmentModel;

class Attachment extends AbstractDb
{
    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var string
     */
    protected $_attachmentCustomerTable;

    /**
     * @var \Magezon\CustomerAttachments\Helper\File
     */
    protected $fileHelper;

    /**
     * @param Context                                  $context        
     * @param EntityManager                            $entityManager  
     * @param MetadataPool                             $metadataPool   
     * @param DateTime                                 $dateTime       
     * @param ManagerInterface                         $eventManager   
     * @param \Magezon\CustomerAttachments\Helper\File $fileHelper     
     * @param string                                   $connectionName 
     */
    public function __construct(
        Context $context,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        DateTime $dateTime,
        ManagerInterface $eventManager,
        \Magezon\CustomerAttachments\Helper\File $fileHelper,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->dateTime      = $dateTime;
        $this->entityManager = $entityManager;
        $this->metadataPool  = $metadataPool;
        $this->_eventManager = $eventManager;
        $this->fileHelper    = $fileHelper;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('customerattachments_attachment', 'attachment_id');
    }

    /**
     * Process attachment data before saving
     *
     * @param AbstractModel $object
     * @return $this
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        foreach (['from_date', 'to_date'] as $field) {
            $value = !$object->getData($field) ? null : $this->dateTime->formatDate($object->getData($field));
            $object->setData($field, $value);
        }
        if ($object->getAttachmentHash() == '') {
            $object->setAttachmentHash($this->fileHelper->getFileHash());
        }
        return parent::_beforeSave($object);
    }

    /**
     * Perform actions after object delete
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject $object
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _afterDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($file = $object->getAttachmentFile()) {
            $this->fileHelper->deleteFile($file);
        }
        return $this;
    }

    /**
     * Process attachment data after save attachment object
     * save related customers ids
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->_saveAttachmentCustomers($object);
        return parent::_afterSave($object);
    }

    /**
     * Save attachment customers relation
     *
     * @param \Magezon\CustomerAttachments\Model\Attachment $attachment
     * @return $this
     */
    protected function _saveAttachmentCustomers($attachment)
    {
        $attachment->setIsChangedCustomerList(false);
        $id        = $attachment->getId();
        $customers = $attachment->getSelectedCustomers();

        /**
         * Example re-save attachment
         */
        if ($customers === null) {
            return $this;
        }

        /**
         * Old attachment-customer relationships
         */
        $oldCustomers = $attachment->getCustomersPosition();
        $insert       = array_diff_key($customers, $oldCustomers);
        $delete       = array_diff_key($oldCustomers, $customers);

        /**
         * Find customer ids which are presented in both arrays
         * and saved before (check $oldCustomers array)
         */
        $update = array_intersect_key($customers, $oldCustomers);
        $update = array_diff_assoc($update, $oldCustomers);

        $connection = $this->getConnection();

        /**
         * Delete customers from attachment
         */
        if (!empty($delete)) {
            $cond = [
                'customer_id IN(?)' => array_keys($delete),
                'attachment_id=?' => $id,
                'type=?' => AttachmentModel::TYPE_FIXED
            ];
            $connection->delete($this->getAttachmentCustomerTable(), $cond);
        }

        /**
         * Add customers to attachment
         */
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $customerId => $position) {
                $data[] = [
                    'number_of_downloads_used' => $this->fileHelper->getNumberOfDownloadsUsed($id, (int)$customerId),
                    'attachment_id'            => (int)$id,
                    'customer_id'              => (int)$customerId,
                    'type'                     => AttachmentModel::TYPE_FIXED,
                    'position'                 => (int)$position
                ];
            }
            $connection->insertMultiple($this->getAttachmentCustomerTable(), $data);
        }

        /**
         * Update rule customers by selected customers
         */
        $where = ['attachment_id = ?' => (int)$id, 'customer_id IN (?)' => array_keys($customers)];
        $bind  = ['type'=> AttachmentModel::TYPE_FIXED];
        $connection->update($this->getAttachmentCustomerTable(), $bind, $where);

        /**
         * Update customer positions in attachment
         */
        if (!empty($update)) {
            $newPositions = [];
            foreach ($update as $customerId => $position) {
                $delta = $position - $oldCustomers[$customerId];
                if (!isset($newPositions[$delta])) {
                    $newPositions[$delta] = [];
                }
                $newPositions[$delta][] = $customerId;
            }

            foreach ($newPositions as $delta => $customerIds) {
                $bind  = [
                    'position' => new \Zend_Db_Expr("position + ({$delta})"),
                    'type'     => AttachmentModel::TYPE_FIXED
                ];
                $where = ['attachment_id = ?' => (int)$id, 'customer_id IN (?)' => $customerIds];
                $connection->update($this->getAttachmentCustomerTable(), $bind, $where);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $customerIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->_eventManager->dispatch(
                'customerattachments_attachment_change_customers',
                ['attachment' => $attachment, 'customer_ids' => $customerIds]
            );

            $attachment->setAffectedCustomerIds($customerIds);
        }

        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $attachment->setIsChangedCustomerList(true);

            /**
             * Setting affected customers to attachment for third party engine index refresh
             */
            $customerIds = array_keys($insert + $delete + $update);
            $attachment->setAffectedCustomerIds($customerIds);
        }
        return $this;
    }

    /**
     * Load an object
     *
     * @param AbstractModel $object
     * @param mixed $value
     * @param string $field field to load by (defaults to model id)
     * @return $this
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $attachmentId = $this->getAttachmentId($object, $value, $field);
        if ($attachmentId) {
            $this->entityManager->load($object, $attachmentId);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }

    /**
     * @param AbstractModel $object
     * @param string $value
     * @param string|null $field
     * @return bool|int|string
     * @throws LocalizedException
     * @throws \Exception
     */
    private function getAttachmentId(AbstractModel $object, $value, $field = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(AttachmentInterface::class);
        if (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }
        $attachmentId = $value;
        if ($field != $entityMetadata->getIdentifierField()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
            ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
            ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $value  = count($result) ? $result[0] : $value;
            $attachmentId = count($result);
        }
        return $attachmentId;
    }

    /**
     * Get website ids to which specified item is assigned
     *
     * @param int $attachmentId
     * @return array
     */
    public function lookupWebsiteIds($attachmentId)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(AttachmentInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['cps' => $this->getTable('customerattachments_attachment_website')], 'website_id')
            ->join(
                ['cp' => $this->getMainTable()],
                'cps.' . $linkField . ' = cp.' . $linkField,
                []
            )
            ->where('cp.' . $entityMetadata->getIdentifierField() . ' = :attachment_id');

        return $connection->fetchCol($select, ['attachment_id' => (int)$attachmentId]);
    }

    /**
     * Retrieve attachment customer ids
     *
     * @param int $attachmentId
     * @return array
     */
    public function getCustomerIds($attachmentId)
    {
        $connection = $this->getConnection();
        $select     = $connection->select()
        ->from($this->getTable('customerattachments_customer_attachment'), ['customer_id'])
        ->where('attachment_id = :attachment_id')
        ->order('position ASC');
        $result = $connection->fetchCol($select, ['attachment_id' => (int) $attachmentId]);
        return $result;
    }

    /**
     * Attachment customer table name getter
     *
     * @return string
     */
    public function getAttachmentCustomerTable()
    {
        if (!$this->_attachmentCustomerTable) {
            $this->_attachmentCustomerTable = $this->getTable('customerattachments_customer_attachment');
        }
        return $this->_attachmentCustomerTable;
    }

    /**
     * Get positions of associated to attachment customers
     *
     * @param int $attachmentId
     * @return array
     */

    /**
     * Get positions of associated to attachment customers
     * 
     * @param  int $attachmentId 
     * @param  string|null $type         
     * @return array             
     */
    public function getCustomersPosition($attachmentId, $type = null)
    {
        $select = $this->getConnection()->select()->from(
            $this->getAttachmentCustomerTable(),
            ['customer_id', 'position']
        )->where(
            'attachment_id = :attachment_id'
        )->order('position ASC');
        if ($type) {
            $select->where('type=?', $type);
        }
        $bind = ['attachment_id' => $attachmentId];

        return $this->getConnection()->fetchPairs($select, $bind);
    }
}
