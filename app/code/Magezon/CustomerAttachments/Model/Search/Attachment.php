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

namespace Magezon\CustomerAttachments\Model\Search;

class Attachment extends \Magento\Framework\DataObject
{
    /**
     * Adminhtml data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_adminhtmlData = null;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $attachmentListRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @param \Magento\Backend\Helper\Data                      $adminhtmlData         
     * @param \Magezon\CustomerAttachments\Api\AttachmentRepositoryInterface $attachmentListRepository   
     * @param \Magento\Framework\Api\SearchCriteriaBuilder      $searchCriteriaBuilder 
     * @param \Magento\Framework\Api\FilterBuilder              $filterBuilder         
     */
    public function __construct(
        \Magento\Backend\Helper\Data $adminhtmlData,
        \Magezon\CustomerAttachments\Api\AttachmentRepositoryInterface $attachmentListRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder
    ) {
        $this->_adminhtmlData           = $adminhtmlData;
        $this->attachmentListRepository = $attachmentListRepository;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
        $this->filterBuilder            = $filterBuilder;
    }

    /**
     * Load search results
     *
     * @return $this
     */
    public function load()
    {
        $result = [];
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($result);
            return $this;
        }

        $this->searchCriteriaBuilder->setCurrentPage($this->getStart());
        $this->searchCriteriaBuilder->setPageSize($this->getLimit());
        $searchFields = ['name'];
        $filters      = [];
        foreach ($searchFields as $field) {
            $filters[] = $this->filterBuilder
                ->setField($field)
                ->setConditionType('like')
                ->setValue($this->getQuery() . '%')
                ->create();
        }
        $this->searchCriteriaBuilder->addFilters($filters);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchResults  = $this->attachmentListRepository->getList($searchCriteria);

        foreach ($searchResults->getItems() as $attachment) {
            $result[] = [
                'id'          => 'attachment/1/' . $attachment->getId(),
                'type'        => __('Attachment'),
                'name'        => $attachment->getName(),
                'description' => '',
                'url'         => $this->_adminhtmlData->getUrl('customerattachments/attachment/edit', ['attachment_id' => $attachment->getId()]),
            ];
        }
        $this->setResults($result);
        return $this;
    }
}
