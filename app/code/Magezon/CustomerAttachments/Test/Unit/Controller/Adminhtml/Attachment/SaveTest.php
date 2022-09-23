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

class SaveTest extends \PHPUnit\Framework\TestCase
{
	/** @var  \Magento\Framework\TestFramework\Unit\Helper\ObjectManager */
	protected $objectManager;

	/** @var  \Magento\Framework\ObjectManager\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
	protected $objectManagerMock;

	/** @var  \Magento\Framework\App\Request\DataPersistorInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $dataPersistorMock;

	/** @var  \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $eventManagerMock;

	/** @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $messageManagerMock;

	/** @var  \Magento\Backend\Model\View\Result\RedirectFactory|\PHPUnit_Framework_MockObject_MockObject */
	protected $resultRedirectFactory;

	/** @var \Magento\Backend\Model\View\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject */
	protected $resultRedirect;

	/** @var \Magezon\CustomerAttachments\Model\AttachmentFactory|\PHPUnit_Framework_MockObject_MockObject */
	protected $attachmentFactory;

	/** @var \Magezon\CustomerAttachments\Api\AttachmentRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $attachmentRepository;

	/** @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $requestMock;

	/** @var \Magezon\CustomerAttachments\Controller\Adminhtml\Attachment\Save */
	protected $saveController;

	/** @var int */
	private $attachmentId = 1;

	protected function setUp()
	{
		$this->objectManager     = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
		$this->objectManagerMock = $this->getMockBuilder(\Magento\Framework\ObjectManager\ObjectManager::class)
            ->setMethods(['create', 'get'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->dataPersistorMock = $this->getMockBuilder(\Magento\Framework\App\Request\DataPersistorInterface::class)
            ->getMockForAbstractClass();

        $this->eventManagerMock = $this->getMockBuilder(\Magento\Framework\Event\ManagerInterface::class)
        	->setMethods(['dispatch'])
        	->getMockForAbstractClass();

       	$this->messageManagerMock = $this->getMockBuilder(\Magento\Framework\Message\ManagerInterface::class)
            ->getMockForAbstractClass();

		$this->resultRedirectFactory = $this->getMockBuilder(\Magento\Backend\Model\View\Result\RedirectFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
		$this->resultRedirect = $this->getMockBuilder(\Magento\Backend\Model\View\Result\Redirect::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultRedirectFactory->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->resultRedirect);

        $this->attachmentFactory = $this->getMockBuilder(\Magezon\CustomerAttachments\Model\AttachmentFactory::class)
			->disableOriginalConstructor()
			->setMethods(['create'])
			->getMock();

		$this->attachmentRepository = $this->getMockBuilder(\Magezon\CustomerAttachments\Api\AttachmentRepositoryInterface::class)
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$this->requestMock = $this->getMockForAbstractClass(
			\Magento\Framework\App\RequestInterface::class,
			[],
			'',
			false,
            true,
            true,
            ['getParam', 'getPostValue']
		);

		$this->saveController = $this->objectManager->getObject(
			\Magezon\CustomerAttachments\Controller\Adminhtml\Attachment\Save::class,
			[
				'request'               => $this->requestMock,
				'attachmentFactory'     => $this->attachmentFactory,
				'attachmentRepository'  => $this->attachmentRepository,
				'resultRedirectFactory' => $this->resultRedirectFactory,
				'messageManager'        => $this->messageManagerMock,
				'dataPersistor'         => $this->dataPersistorMock,
				'eventManager'          => $this->eventManagerMock
			]
		);
	}

	public function testSaveActionWithoutData()
	{
		$this->requestMock->expects($this->once())
			->method('getPostValue')
			->willReturn(false);

		$this->resultRedirect->expects($this->once())
			->method('setPath')
			->with('*/*/')
			->willReturnSelf();

		$this->assertSame($this->resultRedirect, $this->saveController->execute());
	}

	public function testSaveAcitonWithNoId()
	{
		$postData = [
			'name'    => 'Homepage',
			'content' => 'Homepage',
			'stores'  => [0]
		];

		$this->requestMock->expects($this->any())
			->method('getPostValue')
			->willReturn($postData);

		$this->requestMock->expects($this->atLeastOnce())
			->method('getParam')
			->willReturnMap(
				[
					['back', null, true],
					['attachment_id', null, $this->attachmentId]
				]
			);

		$attachment = $this->getMockBuilder(\Magezon\CustomerAttachments\Model\Attachment::class)
        	->disableOriginalConstructor()
        	->getMock();
		$this->attachmentFactory->expects($this->atLeastOnce())
			->method('create')
			->willReturn($attachment);
		$attachment->expects($this->once())
			->method('load')
			->with($this->attachmentId)
			->willReturnSelf();

		$this->messageManagerMock->expects($this->once())
			->method('addErrorMessage')
			->with(__('This attachment no longer exists.'));

		$this->resultRedirect->expects($this->once())
			->method('setPath')
			->with('*/*/')
			->willReturnSelf();

		$this->attachmentRepository->expects($this->never())->method('save');

		$this->assertSame($this->resultRedirect, $this->saveController->execute());
	}

	public function testSaveActionThrowsException()
	{
		$this->requestMock->expects($this->any())
			->method('getPostValue')
			->willReturn([
				'attachment_id'   => $this->attachmentId,
				'attachment_file' => 'demo.jpg'
			]);

		$this->requestMock->expects($this->atLeastOnce())
			->method('getParam')
			->willReturnMap(
				[
					['attachment_id', null, $this->attachmentId],
                	['back', null, true],
				]
			);

		$attachment = $this->getMockBuilder(\Magezon\CustomerAttachments\Model\Attachment::class)
        	->disableOriginalConstructor()
        	->getMock();
        $this->attachmentFactory->expects($this->once())
        	->method('create')
        	->willReturn($attachment);
        $attachment->expects($this->once())
        	->method('load')
        	->willReturnSelf();
        $attachment->expects($this->once())
        	->method('getId')
        	->willReturn(true);
        $attachment->expects($this->once())->method('loadPost');

        $this->attachmentRepository->expects($this->once())
        	->method('save')
        	->with($attachment)
        	->willThrowException(new \Exception('Error message.'));

        $this->messageManagerMock->expects($this->never())->method('addSuccessMessage');

        $this->dataPersistorMock->expects($this->once())
        	->method('set')
        	->with('customerattachments_attachment', [
				'attachment_id'   => $this->attachmentId,
				'attachment_file' => 'demo.jpg'
        	]);

        $this->resultRedirect->expects($this->atLeastOnce())
            ->method('setPath')
        	->with('*/*/edit', ['attachment_id' => $this->attachmentId])
        	->willReturnSelf();

        $this->assertSame($this->resultRedirect, $this->saveController->execute());
	}

	public function testSaveAcion()
	{
		$postData = [
			'name'    => 'Homepage',
			'content' => 'Homepage',
			'stores'  => [0]
		];

		$this->requestMock->expects($this->any())
			->method('getPostValue')
			->willReturn($postData);
		$this->requestMock->expects($this->atLeastOnce())
			->method('getParam')
			->willReturnMap(
				[
					['back', null, true],
					['attachment_id', null, $this->attachmentId]
				]
			);

		$attachment = $this->getMockBuilder(\Magezon\CustomerAttachments\Model\Attachment::class)
        	->disableOriginalConstructor()
        	->getMock();
		$this->attachmentFactory->expects($this->atLeastOnce())
			->method('create')
			->willReturn($attachment);
		$attachment->expects($this->once())
            ->method('load')
            ->willReturnSelf();
        $attachment->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn(true);
        $attachment->expects($this->once())->method('loadPost');

        $this->eventManagerMock->expects($this->once())
        	->method('dispatch')
        	->with('customerattachments_attachment_prepare_save', ['attachment' => $attachment, 'request' => $this->requestMock]);

        $this->attachmentRepository->expects($this->once())
        	->method('save')
        	->with($attachment);

        $this->messageManagerMock->expects($this->once())
        	->method('addSuccessMessage')
        	->with(__('You saved the attachment.'));

        $this->dataPersistorMock->expects($this->once())
        	->method('clear')
        	->with('customerattachments_attachment');

		$this->resultRedirect->expects($this->atLeastOnce())
			->method('setPath')
			->with('*/*/edit', ['attachment_id' => $this->attachmentId])
			->willReturnSelf();

		$this->assertSame($this->resultRedirect, $this->saveController->execute());
	}
}
