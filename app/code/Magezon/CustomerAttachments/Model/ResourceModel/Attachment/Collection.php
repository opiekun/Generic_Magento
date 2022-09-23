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

namespace Magezon\CustomerAttachments\Model\ResourceModel\Attachment;

use Magezon\CustomerAttachments\Api\Data\AttachmentInterface;
use Magezon\CustomerAttachments\Model\ResourceModel\AbstractCollection;
use Magento\Framework\App\ObjectManager;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'attachment_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'customerattachments_attachment_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'attachment_collection';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magezon\CustomerAttachments\Model\Attachment::class, \Magezon\CustomerAttachments\Model\ResourceModel\Attachment::class);
        $this->_map['fields']['attachment_id'] = 'main_table.attachment_id';
        $this->_map['fields']['website']       = 'website_table.website_id';
    }

    /**
     * Add filter by ưebsite
     *
     * @param int|array|\Magento\Store\Model\Website $website
     * @param bool $withAdmin
     * @return $this
     */
    public function addWebsiteFilter($website = null, $withAdmin = true)
    {
        if ($website == NULL) {
            $website = $this->storeManager->getWebsite($website);
        }

        if (!$this->getFlag('website_filter_added')) {
            $this->performAddWebsiteFilter($website, $withAdmin);
        }
        return $this;
    }

    /**
     * Add filter by customer ids
     *
     * @param int|array
     * @return $this
     */
    public function addCustomersFilter($customerIds)
    {
        if (is_string($customerIds)) {
            $customerIds = [$customerIds];
        }

        $entityMetadata = $this->metadataPool->getMetadata(AttachmentInterface::class);
        $linkField = $entityMetadata->getLinkField();
        $this->getSelect()->joinLeft(
            ['cca' => $this->getTable('customerattachments_customer_attachment')],
            'main_table.' . $linkField . ' = cca.' . $linkField,
            ['position', 'number_of_downloads_used']
        )->where(
            'cca.customer_id IN (?)', $customerIds
        )->group(
            'main_table.' . $linkField
        );

        return $this;
    }

    /**
     * Add filter by from date, to date
     *
     * @return $this
     */
    public function addDateToFilter()
    {
        $todayStartOfDayDate = $this->getLocaleDate()->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate   = $this->getLocaleDate()->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        $this->addFieldToFilter(
            'from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addFieldToFilter(
            'to_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayStartOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        );

        return $this;
    }

    /**
     * Add active attachment filter
     *
     * @return $this
     */
    public function addIsActiveFilter()
    {
        $this->addFieldToFilter('is_active', 1);
        return $this;
    }

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $entityMetadata = $this->metadataPool->getMetadata(AttachmentInterface::class);
        $this->performAfterLoad('customerattachments_attachment_website', $entityMetadata->getLinkField());

        return parent::_afterLoad();
    }

    /**
     * Perform operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $entityMetadata = $this->metadataPool->getMetadata(AttachmentInterface::class);
        $this->joinWebsiteRelationTable('customerattachments_attachment_website', $entityMetadata->getLinkField());
    }

    /**
     * @return \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected function getLocaleDate()
    {
        if (null === $this->_localeDate) {
            $this->_localeDate = ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\DateTime\TimezoneInterface::class
            );
        }
        return $this->_localeDate;
    }

    public function addTotalDownloads()
    {
        $connection = $this->getConnection();
        $select     = $connection->select()->from(['ccar' => $this->getTable('customerattachments_customer_attachment_report')])->where('ccar.attachment_id = ccar.attachment_id');
        $result     = $connection->fetchAll($select);

        if ($result) {
            foreach ($this as $item) {
                $downloads = 0;
                foreach ($result as $_reportData) {
                    if ($_reportData['attachment_id'] == $item->getId()) {
                        $downloads++;
                    }
                }
                $item->setData('downloads', $downloads);
            }
        }
    }

    /**
     * Add select order
     *
     * @param   string $field
     * @param   string $direction
     * @return  $this
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if ($field == 'downloads') {
            $this->getSelect()->joinLeft(
                ['ccar' => $this->getTable('customerattachments_customer_attachment_report')],
                'main_table.attachment_id = ccar.attachment_id',
                ['downloads' => 'COUNT(ccar.customer_id)']
            )->group(
                'main_table.attachment_id'
            );
        }
        return $this->_setOrder($field, $direction);
    }

    /**
     * Add ORDER BY to the end or to the beginning
     *
     * @param string $field
     * @param string $direction
     * @param bool $unshift
     * @return $this
     */
    private function _setOrder($field, $direction, $unshift = false)
    {
        $this->_isOrdersRendered = false;
        $field = (string)$this->_getMappedField($field);
        $direction = strtoupper($direction) == self::SORT_ORDER_ASC ? self::SORT_ORDER_ASC : self::SORT_ORDER_DESC;

        unset($this->_orders[$field]);
        // avoid ordering by the same field twice
        if ($unshift) {
            $orders = [$field => $direction];
            foreach ($this->_orders as $key => $dir) {
                $orders[$key] = $dir;
            }
            $this->_orders = $orders;
        } else {
            $this->_orders[$field] = $direction;
        }
        return $this;
    }
}
