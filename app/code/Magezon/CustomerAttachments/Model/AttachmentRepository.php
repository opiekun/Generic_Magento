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

namespace Magezon\CustomerAttachments\Model;

use Magezon\CustomerAttachments\Api\Data;
use Magezon\CustomerAttachments\Api\AttachmentRepositoryInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magezon\CustomerAttachments\Model\ResourceModel\Attachment as ResourceAttachment;
use Magezon\CustomerAttachments\Model\ResourceModel\Attachment\CollectionFactory as AttachmentCollectionFactory;

class AttachmentRepository implements AttachmentRepositoryInterface
{
    /**
     * @var ResourceAttachment
     */
    protected $resource;

    /**
     * @var AttachmentFactory
     */
    protected $attachmentFactory;

    /**
     * @var AttachmentCollectionFactory
     */
    protected $attachmentCollectionFactory;

    /**
     * @var Data\AttachmentSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var \Magezon\Core\Helper\Api
     */
    private $apiHelper;

    /**
     * @var \Magezon\CustomerAttachments\Helper\File
     */
    private $fileHelper;

    /**
     * @var array
     */
    private $attachments = [];

    /**
     * @param ResourceAttachment                           $resource                    
     * @param AttachmentFactory                            $attachmentFactory           
     * @param AttachmentCollectionFactory                  $attachmentCollectionFactory 
     * @param Data\AttachmentSearchResultsInterfaceFactory $searchResultsFactory        
     * @param CollectionProcessorInterface                 $collectionProcessor         
     * @param \Magezon\Core\Helper\Api                     $apiHelper                   
     * @param \Magezon\CustomerAttachments\Helper\File     $fileHelper                  
     */
    public function __construct(
        ResourceAttachment $resource,
        AttachmentFactory $attachmentFactory,
        AttachmentCollectionFactory $attachmentCollectionFactory,
        Data\AttachmentSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        \Magezon\Core\Helper\Api $apiHelper,
        \Magezon\CustomerAttachments\Helper\File $fileHelper
    ) {
        $this->resource                    = $resource;
        $this->attachmentFactory           = $attachmentFactory;
        $this->attachmentCollectionFactory = $attachmentCollectionFactory;
        $this->searchResultsFactory        = $searchResultsFactory;
        $this->collectionProcessor         = $collectionProcessor;
        $this->apiHelper                   = $apiHelper;
        $this->fileHelper                  = $fileHelper;
    }

    /**
     * Save attachment.
     *
     * @param \Magezon\CustomerAttachments\Api\Data\AttachmentInterface $attachment
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magezon\CustomerAttachments\Api\Data\AttachmentInterface $attachment)
    {
    	try {
            if ($fileContent = $attachment->getAttachmentFileContent()) {
                $attachmentFile = $this->apiHelper->imagePreprocessing($fileContent, $this->fileHelper->getBaseMediaPath(), true);
                $attachment->setAttachmentFile($attachmentFile);
            }
            if ($attachment->getId()) {
                $attachment = $this->getById($attachment->getId())->addData($attachment->getData());
            }
            $this->resource->save($attachment);
            unset($this->attachments[$attachment->getId()]);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the attachment: %1', $exception->getMessage()),
                $exception
            );
        }
        return $attachment;
    }

    /**
     * Retrieve attachment.
     *
     * @param int $attachmentId
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($attachmentId)
    {
        if (!isset($this->attachments[$attachmentId])) {

            /** @var \Magezon\CustomerAttachments\Model\Attachment $attachment */
            $attachment = $this->attachmentFactory->create();
            $attachment->load($attachmentId);
            if (!$attachment->getId()) {
                throw new NoSuchEntityException(__('Attachment with id "%1" does not exist.', $attachmentId));
            }
            $this->attachments[$attachmentId] = $attachment;
        }
        return $this->attachments[$attachmentId];
    }

    /**
     * Retrieve attachments matching the specified searchCriteria.
     *
     * @param int $customerId
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCustomerId($customerId)
    {
        /** @var \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\Collection $collection */
        $collection = $this->fileHelper->getCustomerAttachments((int)$customerId);

        /** @var Data\AttachmentSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Load SLider data collection by given search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\Collection $collection */
        $collection = $this->attachmentCollectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var Data\AttachmentSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Delete attachment.
     *
     * @param \Magezon\CustomerAttachments\Api\Data\AttachmentInterface $attachment
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magezon\CustomerAttachments\Api\Data\AttachmentInterface $attachment)
    {
        try {
            $this->resource->delete($attachment);
            unset($this->attachments[$attachment->getId()]);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the attachment: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * Delete attachment by ID.
     *
     * @param int $attachmentId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($attachmentId)
    {
        return $this->delete($this->getById($attachmentId));
    }
}
