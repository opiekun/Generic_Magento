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

use \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\Relation\Website\ReadHandler;

class ReadHandlerTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * @var ReadHandler
	 */
	protected $model;

	/**
	 * @var \Magezon\CustomerAttachments\Model\ResourceModel\Attachment
	 */
	protected $resourceAttachment;

	protected function setUp()
	{
        $this->resourceAttachment = $this->getMockBuilder(\Magezon\CustomerAttachments\Model\ResourceModel\Attachment::class)
        	->disableOriginalConstructor()
            ->getMock();

        $this->model = new ReadHandler(
            $this->resourceAttachment
        );
	}

	public function testExecute()
	{
		$entityId = 1;
		$websiteId  = 1;

		$this->resourceAttachment->expects($this->once())
			->method('lookupWebsiteIds')
			->with($entityId)
			->willReturn([$websiteId]);

		$attachment = $this->getMockBuilder(\Magezon\CustomerAttachments\Model\Attachment::class)
			->disableOriginalConstructor()
			->setMethods(['getId', 'setData'])
			->getMock();

		$attachment->expects($this->exactly(2))
			->method('getId')
			->willReturn($entityId);
		$attachment->expects($this->once())
			->method('setData')
			->with('website_id', [$websiteId])
			->willReturnSelf();

		$result = $this->model->execute($attachment);
        $this->assertInstanceOf(\Magezon\CustomerAttachments\Api\Data\AttachmentInterface::class, $result);
	}

	public function testExecuteWithNoId()
	{
		$attachment = $this->getMockBuilder(\Magezon\CustomerAttachments\Model\Attachment::class)
			->disableOriginalConstructor()
			->setMethods(['getId'])
			->getMock();
		$attachment->expects($this->once())
			->method('getId')
			->willReturn(false);

		$this->resourceAttachment->expects($this->never())->method('lookupWebsiteIds');

		$result = $this->model->execute($attachment);
		$this->assertInstanceOf(\Magezon\CustomerAttachments\Api\Data\AttachmentInterface::class, $result);
	}
}
