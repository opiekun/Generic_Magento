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

namespace Magezon\CustomerAttachments\Test\Unit\Controller\Adminhtml\Attachment;

class DeleteTest extends \PHPUnit\Framework\TestCase
{
	/** @var \Magezon\CustomerAttachments\Controller\Adminhtml\Attachment\Delete */
	protected $deleteController;

	/** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager */
	protected $objectManager;

	/** @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject */
	protected $contextMock;

	/** @var \Magento\Backend\Model\View\Result\RedirectFactory|\PHPUnit_Framework_MockObject_MockObject */
	protected $resultRedirectFactoryMock;

	/** @var \Magento\Backend\Model\View\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject */
	protected $resultRedirectMock;

	/** @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $messageManagerMock;

	/** @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $requestMock;

	/** @var \Magento\Framework\ObjectManager\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
	protected $objectManagerMock;

	/** @var [type] [description] */
	protected $attachmentMock;

	/** @var int */
	protected $attachmentId = 1;

	protected function setUp()
	{
		$this->objectManager      = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
		
		$this->messageManagerMock = $this->createMock(\Magento\Framework\Message\ManagerInterface::class);
		
		$this->objectManagerMock  = $this->getMockBuilder(\Magento\Framework\ObjectManager\ObjectManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->resultRedirectMock = $this->getMockBuilder(\Magento\Backend\Model\View\Result\Redirect::class)
            ->setMethods(['setPath'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultRedirectFactoryMock = $this->getMockBuilder(
            \Magento\Backend\Model\View\Result\RedirectFactory::class
        )->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->resultRedirectFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->resultRedirectMock);

		$this->requestMock = $this->getMockForAbstractClass(
			\Magento\Framework\App\RequestInterface::class,
			[],
			'',
			false,
            true,
            true,
            ['getParam']
		);

		$this->attachmentMock = $this->getMockBuilder(\Magezon\CustomerAttachments\Model\Attachment::class)
			->disableOriginalConstructor()
			->setMethods(['load', 'delete'])
			->getMock();

		$this->contextMock = $this->createMock(\Magento\Backend\App\Action\Context::class);
        $this->contextMock->expects($this->any())->method('getRequest')->willReturn($this->requestMock);
        $this->contextMock->expects($this->any())->method('getMessageManager')->willReturn($this->messageManagerMock);
        $this->contextMock->expects($this->any())->method('getObjectManager')->willReturn($this->objectManagerMock);
        $this->contextMock->expects($this->any())->method('getResultRedirectFactory')->willReturn($this->resultRedirectFactoryMock);

		$this->deleteController = $this->objectManager->getObject(
            \Magezon\CustomerAttachments\Controller\Adminhtml\Attachment\Delete::class,
            [
                'context' => $this->contextMock,
            ]
        );
	}

	public function testDeleteAction()
	{
		$this->requestMock->expects($this->once())
			->method('getParam')
			->willReturn($this->attachmentId);

		$this->objectManagerMock->expects($this->once())
			->method('create')
			->with(\Magezon\CustomerAttachments\Model\Attachment::class)
			->willReturn($this->attachmentMock);

		$this->attachmentMock->expects($this->once())
			->method('load')
			->with($this->attachmentId);
		$this->attachmentMock->expects($this->once())
			->method('delete');

		$this->messageManagerMock->expects($this->once())
			->method('addSuccess')
			->with(__('The attachment has been deleted.'));
		$this->messageManagerMock->expects($this->never())
			->method('addError');

		$this->resultRedirectMock->expects($this->once())
			->method('setPath')
			->with('*/*/')
			->willReturnSelf();

		$this->assertSame($this->resultRedirectMock, $this->deleteController->execute());
	}

	public function testDeleteActionWithNoId()
	{
		$this->requestMock->expects($this->once())
			->method('getParam')
			->willReturn(null);

		$this->messageManagerMock->expects($this->once())
			->method('addError')
			->with(__('We can\'t find a attachment to delete.'));
		$this->messageManagerMock->expects($this->never())
			->method('addSuccess');

		$this->resultRedirectMock->expects($this->once())
			->method('setPath')
			->with('*/*/')
			->willReturnSelf();

		$this->assertSame($this->resultRedirectMock, $this->deleteController->execute());
	}

	public function testDeleteActionThrowException()
	{
		$errorMsg = __('Can\'t delete the page');

		$this->requestMock->expects($this->once())
			->method('getParam')
			->willReturn($this->attachmentId);

		$this->objectManagerMock->expects($this->once())
			->method('create')
			->with(\Magezon\CustomerAttachments\Model\Attachment::class)
			->willReturn($this->attachmentMock);

		$this->attachmentMock->expects($this->once())
			->method('load')
			->with($this->attachmentId);
		$this->attachmentMock->expects($this->once())
			->method('delete')
			->willThrowException(new \Exception($errorMsg));

		$this->messageManagerMock->expects($this->once())
			->method('addError')
			->with($errorMsg);
		$this->messageManagerMock->expects($this->never())
			->method('addSuccess');

		$this->resultRedirectMock->expects($this->once())
			->method('setPath')
			->with('*/*/edit', ['attachment_id' => $this->attachmentId])
			->willReturnSelf();

		$this->assertSame($this->resultRedirectMock, $this->deleteController->execute());
	}

} 
