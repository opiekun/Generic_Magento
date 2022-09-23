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

use Magento\Store\Model\Store;
use Magento\Store\Model\Website;

abstract class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->storeManager = $storeManager;
        $this->metadataPool = $metadataPool;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Perform operations after collection load
     *
     * @param string $tableName
     * @param string|null $linkField
     * @return void
     */
    protected function performAfterLoad($tableName, $linkField)
    {
        $linkedIds = $this->getColumnValues($linkField);
        if (count($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['customerattachments_entity_website' => $this->getTable($tableName)])
                ->where('customerattachments_entity_website.' . $linkField . ' IN (?)', $linkedIds);
            $result = $connection->fetchAll($select);
            if ($result) {
                $websitesData = [];
                foreach ($result as $websiteData) {
                    $websitesData[$websiteData[$linkField]][] = $websiteData['website_id'];
                }

                $defaultId = $this->storeManager->getWebsite()->getId();

                foreach ($this as $item) {
                    $linkedId = $item->getData($linkField);
                    if (!isset($websitesData[$linkedId])) {
                        continue;
                    }
                    $websiteIdKey = array_search($defaultId, $websitesData[$linkedId], true);
                    if ($websiteIdKey !== false) {
                        $websites    = $this->storeManager->getWebsites(false, true);
                        $websiteId   = current($websites)->getId();
                        $websiteCode = key($websites);
                    } else {
                        $websiteId   = current($websitesData[$linkedId]);
                        $websiteCode = $this->storeManager->getWebsite($websiteId)->getCode();
                    }
                    $item->setData('_first_website_id', $websiteId);
                    $item->setData('website_code', $websiteCode);
                    $item->setData('website_id', $websitesData[$linkedId]);
                }
            }
        }
    }

    /**
     * Add filter by website
     *
     * @param int|array|Website $website
     * @param bool $withAdmin
     * @return $this
     */
    abstract public function addWebsiteFilter($website, $withAdmin = true);

    /**
     * Perform adding filter by website
     *
     * @param int|array|Website $website
     * @param bool $withAdmin
     * @return void
     */
    protected function performAddWebsiteFilter($website, $withAdmin = true)
    {
        if ($website instanceof Website) {
            $website = [$website->getId()];
        }

        if (!is_array($website)) {
            $website = [$website];
        }

        if ($withAdmin) {
            $website[] = $this->storeManager->getWebsite()->getId();
        } 

        $this->addFilter('website', ['in' => $website], 'public');
    }

    /**
     * Join website relation table if there is website filter
     *
     * @param string $tableName
     * @param string|null $linkField
     * @return void
     */
    protected function joinWebsiteRelationTable($tableName, $linkField)
    {
        if ($this->getFilter('website')) {
            $this->getSelect()->join(
                ['website_table' => $this->getTable($tableName)],
                'main_table.' . $linkField . ' = website_table.' . $linkField,
                []
            )->group(
                'main_table.' . $linkField
            );
        }
        parent::_renderFiltersBefore();
    }

    /**
     * Get SQL for get record count
     *
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);

        return $countSelect;
    }
}
