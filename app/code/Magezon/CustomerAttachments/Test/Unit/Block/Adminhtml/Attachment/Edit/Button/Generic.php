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

namespace Magezon\CustomerAttachments\Test\Unit\Block\Adminhtml\Attachment\Edit\Button;

use Magezon\CustomerAttachments\Api\Data\AttachmentInterface;

class Generic extends \PHPUnit\Framework\TestCase
{
	/** @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $objectManager;

	/** @var \Magento\Framework\View\Element\UiComponent\Context|\PHPUnit_Framework_MockObject_MockObject */
	protected $contextMock;

	/** @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject */
	protected $registryMock;

	/** @var \Magento\Framework\AuthorizationInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $authorizationMock;

	/** @var \Magezon\CustomerAttachments\Api\Data\AttachmentInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $attachmentMock;

	protected function setUp()
	{
		$this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
		
		$this->contextMock   = $this->createMock(\Magento\Framework\View\Element\UiComponent\Context::class);
		
		$this->registryMock  = $this->getMockBuilder(\Magento\Framework\Registry::class)
			->disableOriginalConstructor()
			->getMock();
		$this->authorizationMock = $this->getMockBuilder(\Magento\Framework\AuthorizationInterface::class)
			->disableOriginalConstructor()
			->getMock();
		$this->attachmentMock = $this->getMockBuilder(\Magezon\CustomerAttachments\Api\Data\AttachmentInterface::class)
			->setMethods(['getId'])
			->getMockForAbstractClass();

		$this->registryMock->expects($this->any())
			->method('registry')
			->with('customerattachments_attachment')
			->willReturn($this->attachmentMock);
	}

	protected function getModel($class = \Magezon\CustomerAttachments\Block\Adminhtml\Attachment\Edit\Button\Generic::class)
	{
		return $this->objectManager->getObject(
			$class,
			[
				'context'       => $this->contextMock,
				'registry'      => $this->registryMock,
				'authorization' => $this->authorizationMock
			]
		);
	}

	public function testGetUrl()
	{
		$this->contextMock->expects($this->once())
			->method('getUrl')
			->willReturn('test_url');

		$this->assertSame('test_url', $this->getModel()->getUrl());
	}

	public function testGetAttachment()
	{
		$this->assertInstanceOf(AttachmentInterface::class, $this->getModel()->getAttachment());
	}
}
