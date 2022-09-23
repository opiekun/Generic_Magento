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

namespace Magezon\CustomerAttachments\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface AttachmentSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get attachments list.
     *
     * @return \Magezon\CustomerAttachments\Api\Data\AttachmentInterface[]
     */
    public function getItems();

    /**
     * Set attachments list.
     *
     * @param \Magezon\CustomerAttachments\Api\Data\AttachmentInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
