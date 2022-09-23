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

namespace Magezon\CustomerAttachments\Model\ResourceModel\Attachment\Relation\Website;

use Magezon\CustomerAttachments\Model\ResourceModel\Attachment;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

class ReadHandler implements ExtensionInterface
{
    /**
     * @var Attachment
     */
    protected $resourceAttachment;

    /**
     * @param Attachment $resourceAttachment
     */
    public function __construct(
        Attachment $resourceAttachment
    ) {
        $this->resourceAttachment = $resourceAttachment;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getId()) {
            $websites = $this->resourceAttachment->lookupWebsiteIds((int)$entity->getId());
            $entity->setData('website_id', $websites);
        }
        return $entity;
    }
}
