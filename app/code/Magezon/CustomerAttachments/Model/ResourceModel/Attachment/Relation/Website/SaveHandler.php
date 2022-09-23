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

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magezon\CustomerAttachments\Api\Data\AttachmentInterface;
use Magezon\CustomerAttachments\Model\ResourceModel\Attachment;
use Magento\Framework\EntityManager\MetadataPool;

class SaveHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var Attachment
     */
    protected $resourceAttachment;

    /**
     * @param MetadataPool $metadataPool
     * @param Attachment $resourceAttachment
     */
    public function __construct(
        MetadataPool $metadataPool,
        Attachment $resourceAttachment
    ) {
        $this->metadataPool       = $metadataPool;
        $this->resourceAttachment = $resourceAttachment;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        $entityMetadata = $this->metadataPool->getMetadata(AttachmentInterface::class);
        $linkField      = $entityMetadata->getLinkField();
        $connection     = $entityMetadata->getEntityConnection(); 
        $oldWebsites    = $this->resourceAttachment->lookupWebsiteIds((int)$entity->getId());
        $newWebsites    = (array)$entity->getWebsites();
        if (empty($newWebsites)) {
            $newWebsites = (array)$entity->getWebsiteId();
        }

        if (!empty($newWebsites)) {
            $table = $this->resourceAttachment->getTable('customerattachments_attachment_website');

            $delete = array_diff($oldWebsites, $newWebsites);
            if ($delete) {
                $where = [
                    $linkField . ' = ?' => (int)$entity->getData($linkField),
                    'website_id IN (?)' => $delete,
                ];
                $connection->delete($table, $where);
            }

            $insert = array_diff($newWebsites, $oldWebsites);
            if ($insert) {
                $data = [];
                foreach ($insert as $websiteId) {
                    $data[] = [
                        $linkField   => (int)$entity->getData($linkField),
                        'website_id' => (int)$websiteId
                    ];
                }
                $connection->insertMultiple($table, $data);
            }
        }

        return $entity;
    }
}
