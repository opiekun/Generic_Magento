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

namespace Magezon\CustomerAttachments\Model\ResourceModel\Attachment\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\AggregationInterface;
use Magezon\CustomerAttachments\Model\ResourceModel\Attachment\Collection as AttachmentCollection;

class Collection extends AttachmentCollection implements SearchResultInterface
{
    /**
     * @var AggregationInterface
     */
    protected $aggregations;

    /**
     * @var \Magezon\CustomerAttachments\Helper\File
     */
    protected $fileHelper;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory 
     * @param \Psr\Log\LoggerInterface                                     $logger        
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy 
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager  
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager  
     * @param \Magento\Framework\EntityManager\MetadataPool                $metadataPool  
     * @param \Magezon\CustomerAttachments\Helper\File                     $fileHelper    
     * @param mixed|null                                                   $mainTable     
     * @param string                                                       $eventPrefix   
     * @param mixed                                                        $eventObject   
     * @param mixed                                                        $resourceModel 
     * @param string                                                       $model         
     * @param null                                                         $connection    
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null    $resource      
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Magezon\CustomerAttachments\Helper\File $fileHelper,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = \Magento\Framework\View\Element\UiComponent\DataProvider\Document::class,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $storeManager,
            $metadataPool,
            $connection,
            $resource
        );
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
        $this->fileHelper   = $fileHelper;
    }

    /**
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->addTotalDownloads();
        foreach ($this as $_item) {
            if ($_item->getAttachmentFile()) {
                $_file = $this->fileHelper->getFileFromPathFile($_item->getAttachmentFile());
                $_item->setAttachmentFile($_file);
            }
        }
        return parent::_afterLoad();
    }
}
