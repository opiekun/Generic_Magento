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

namespace Magezon\CustomerAttachments\Test\Unit\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magezon\CustomerAttachments\Model\ResourceModel\Attachment as AttachResourceModel;
use Magezon\CustomerAttachments\Model\Attachment;

class AttachmentTest extends \PHPUnit\Framework\TestCase
{
	protected function setUp()
	{
		$this->contextMock = $this->getMockBuilder(Context::class)
			->disableOriginalConstructor()
			->getMock();
		$this->entityManagerMock = $this->getMockBuilder(EntityManager::class)
			->disableOriginalConstructor()
			->getMock();
		$this->metadataPoolMock = $this->getMockBuilder(MetadataPool::class)
			->disableOriginalConstructor()
			->getMock();
		$this->dateTimeMock = $this->getMockBuilder(DateTime::class)
			->disableOriginalConstructor()
			->getMock();
		$this->attachmentMock = $this->getMockBuilder(Attachment::class)
			->disableOriginalConstructor()
			->getMock();

		$this->model = (new ObjectManager($this))->getObject(AttachResourceModel::class, [
			'context'       => $this->contextMock,
			'entityManager' => $this->entityManagerMock,
			'metadataPool'  => $this->metadataPoolMock,
			'dateTime'      => $this->dateTimeMock
		]);
	}

	public function testBeforeSave()
	{
		$this->attachmentMock->expects($this->any())
			->method('getData')
			->willReturnMap([
				['from_date', null, null],
				['to_date', null, '01/10/2018']
			]);
		$this->dateTimeMock->expects($this->once())
			->method('formatDate')
			->with('01/10/2018')
			->willReturn('01 Jan 2018');
		$this->attachmentMock->expects($this->any())
			->method('setData')
			->withConsecutive(
				['from_date', null],
				['to_date', '01 Jan 2018']
			);

		$this->model->beforeSave($this->attachmentMock);
	}
}
