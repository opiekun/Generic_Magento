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

namespace Magezon\CustomerAttachments\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Attachment CRUD interface.
 * @api
 */
interface AttachmentRepositoryInterface
{
    /**
     * Save attachment.
     *
     * @param \Magezon\CustomerAttachments\Api\Data\AttachmentInterface $attachment
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magezon\CustomerAttachments\Api\Data\AttachmentInterface $attachment);

    /**
     * Retrieve attachment.
     *
     * @param int $attachmentId
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($attachmentId);

    /**
     * Retrieve attachments matching the specified searchCriteria.
     *
     * @param int $customerId
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCustomerId($customerId);

    /**
     * Retrieve attachments matching the specified searchCriteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete attachment.
     *
     * @param \Magezon\CustomerAttachments\Api\Data\AttachmentInterface $attachment
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magezon\CustomerAttachments\Api\Data\AttachmentInterface $attachment);

    /**
     * Delete attachment by ID.
     *
     * @param int $attachmentId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($attachmentId);
}
