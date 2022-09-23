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

namespace Magezon\CustomerAttachments\Test\Unit\Model\ResourceModel\Attachment\Relation;

use \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\Relation\Website\SaveHandler;

class SaveHandlerTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * @var SaveHandler
	 */
	protected $model;

	/**
	 * @var \Magento\Framework\EntityManager\MetadataPool
	 */
	protected $metadataPool;

	/**
	 * @var \Magezon\CustomerAttachments\Model\ResourceModel\Attachment
	 */
	protected $resourceAttachment;

	protected function setUp()
	{
		$this->metadataPool = $this->getMockBuilder(\Magento\Framework\EntityManager\MetadataPool::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resourceAttachment = $this->getMockBuilder(\Magezon\CustomerAttachments\Model\ResourceModel\Attachment::class)
        	->disableOriginalConstructor()
            ->getMock();

        $this->model = new SaveHandler(
            $this->metadataPool,
            $this->resourceAttachment
        );
	}

	public function testExecute()
	{
        $entityId   = 1;
        $linkField  = 'entity_id';
        $oldWebsite = 1;
        $newWebsite = 2;
        $linkId     = 2;

		$adapter = $this->getMockBuilder(\Magento\Framework\DB\Adapter\AdapterInterface::class)
            ->getMockForAbstractClass();

        $whereForDelete = [
        	$linkField . ' = ?' => $linkId,
            'website_id IN (?)' => [$oldWebsite],
        ];
        $adapter->expects($this->once())
        	->method('delete')
        	->with('customerattachments_attachment_website', $whereForDelete)
        	->willReturnSelf();

        $whereForInsert = [
        	$linkField => $linkId,
        	'website_id' => $newWebsite
        ];
        $adapter->expects($this->once())
        	->method('insertMultiple')
        	->with('customerattachments_attachment_website', [$whereForInsert])
        	->willReturnSelf();

        $entityMetadata = $this->getMockBuilder(\Magento\Framework\EntityManager\EntityMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entityMetadata->expects($this->once())
        	->method('getLinkField')
        	->willReturn($linkField);
        $entityMetadata->expects($this->once())
        	->method('getEntityConnection')
        	->willReturn($adapter);

        $this->metadataPool->expects($this->once())
        	->method('getMetadata')
        	->with(\Magezon\CustomerAttachments\Api\Data\AttachmentInterface::class)
        	->willReturn($entityMetadata);

        $this->resourceAttachment->expects($this->once())
            ->method('lookupWebsiteIds')
            ->willReturn([$oldWebsite]);
        $this->resourceAttachment->expects($this->once())
            ->method('getTable')
            ->with('customerattachments_attachment_website')
            ->willReturn('customerattachments_attachment_website');

        $attachment = $this->getMockBuilder(\Magezon\CustomerAttachments\Model\Attachment::class)
        	->disableOriginalConstructor()
        	->setMethods([
                'getWebsites',
                'getWebsiteId',
                'getId',
                'getData',
            ])
            ->getMock();
        $attachment->expects($this->once())
            ->method('getWebsites')
            ->willReturn(null);
        $attachment->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($newWebsite);
        $attachment->expects($this->once())
            ->method('getId')
            ->willReturn($entityId);
        $attachment->expects($this->exactly(2))
            ->method('getData')
            ->with($linkField)
            ->willReturn($linkId);

        $result = $this->model->execute($attachment);
        $this->assertInstanceOf(\Magezon\CustomerAttachments\Api\Data\AttachmentInterface::class, $result);
	}
}
