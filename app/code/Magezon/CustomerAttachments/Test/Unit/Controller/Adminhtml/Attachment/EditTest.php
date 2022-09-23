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

class EditTest extends \PHPUnit\Framework\TestCase
{
	/** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager */
	protected $objectManager;

	/** @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $messageManagerMock;

	/** @var \Magento\Framework\View\Result\PageFactory|\PHPUnit_Framework_MockObject_MockObject */
	protected $resultPageFactoryMock;

	/** @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject */
	protected $coreRegistryMock;

	/** @var \Magezon\CustomerAttachments\Model\Attachment|\PHPUnit_Framework_MockObject_MockObject */
	protected $attachmentMock;

	/** @var \Magento\Framework\ObjectManager\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
	protected $objectManagerMock;

	/** @var \Magento\Backend\Model\View\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject */
	protected $resultRedirectMock;

	/** @var \Magento\Backend\Model\View\Result\RedirectFactory|\PHPUnit_Framework_MockObject_MockObject */
	protected $resultRedirectFactoryMock;

	/** @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $requestMock;

	/** @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject */
	protected $contextMock;

	/** @var \Magezon\CustomerAttachments\Controller\Adminhtml\Attachment\Edit|\PHPUnit_Framework_MockObject_MockObject */
	protected $editController;

	/** @var int */
	protected $attachmentId = 1;

	public function setUp()
	{
		$this->objectManager         = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
		$this->messageManagerMock    = $this->createMock(\Magento\Framework\Message\ManagerInterface::class);
		$this->resultPageFactoryMock = $this->createMock(\Magento\Framework\View\Result\PageFactory::class);
		$this->coreRegistryMock      = $this->createMock(\Magento\Framework\Registry::class);
		$this->importHelperMock      = $this->createMock(\Magento\ImportExport\Helper\Data::class);

		$this->attachmentMock = $this->getMockBuilder(\Magezon\CustomerAttachments\Model\Attachment::class)
			->disableOriginalConstructor()
			->getMock();

		$this->objectManagerMock = $this->getMockBuilder(\Magento\Framework\ObjectManager\ObjectManager::class)
            ->setMethods(['create', 'get'])
            ->disableOriginalConstructor()
            ->getMock();
		$this->objectManagerMock->expects($this->once())
			->method('create')
			->with(\Magezon\CustomerAttachments\Model\Attachment::class)
			->willReturn($this->attachmentMock);
		$this->objectManagerMock->expects($this->once())
			->method('get')
			->with(\Magento\ImportExport\Helper\Data::class)
			->willReturn($this->attachmentMock);

		$this->resultRedirectMock = $this->getMockBuilder(\Magento\Backend\Model\View\Result\Redirect::class)
			->disableOriginalConstructor()
			->getMock();
		
		$this->resultRedirectFactoryMock = $this->getMockBuilder(\Magento\Backend\Model\View\Result\RedirectFactory::class)
			->disableOriginalConstructor()
			->getMock();

		$this->sessionMock = $this->getMockBuilder(\Magento\Backend\Model\Session::class)
			->disableOriginalConstructor()
			->getMock();

		$this->requestMock = $this->getMockForAbstractClass(
			\Magento\Framework\App\RequestInterface::class,
			[],
			'',
			false,
            true,
            true,
            ['getParam']
		);

		$this->contextMock = $this->createMock(\Magento\Backend\App\Action\Context::class);
		$this->contextMock->expects($this->once())->method('getRequest')->willReturn($this->requestMock);
		$this->contextMock->expects($this->once())->method('getObjectManager')->willReturn($this->objectManagerMock);
		$this->contextMock->expects($this->once())->method('getMessageManager')->willReturn($this->messageManagerMock);
		$this->contextMock->expects($this->once())->method('getSession')->willReturn($this->sessionMock);
		$this->contextMock->expects($this->once())
			->method('getResultRedirectFactory')
			->willReturn($this->resultRedirectFactoryMock);

		$this->editController = $this->objectManager->getObject(
			\Magezon\CustomerAttachments\Controller\Adminhtml\Attachment\Edit::class,
			[
				'context'           => $this->contextMock,
				'resultPageFactory' => $this->resultPageFactoryMock,
				'registry'          => $this->coreRegistryMock
			]
		);
	}

	public function testEditActionPageNoExist()
	{
		$this->requestMock->expects($this->once())
			->method('getParam')
			->with('attachment_id')
			->willReturn($this->attachmentId);

		$this->objectManagerMock->expects($this->once())
			->method('create')
			->with(\Magezon\CustomerAttachments\Model\Attachment::class)
			->willReturn($this->attachmentMock);

		$this->attachmentMock->expects($this->once())
			->method('load')
			->with($this->attachmentId);
		$this->attachmentMock->expects($this->once())
			->method('getId')
			->willReturn(null);

		$this->messageManagerMock->expects($this->once())
			->method('addError')
			->with(__('This attachment no longer exists.'));

		$this->resultRedirectFactoryMock->expects($this->once())
			->method('create')
			->willReturn($this->resultRedirectMock);

		$this->resultRedirectMock->expects($this->once())
			->method('setPath')
			->with('*/*/')
			->willReturnSelf();

		$this->assertSame($this->resultRedirectMock, $this->editController->execute());
	}

	/**
	 * @param  int $attachmentId
	 * @param  string $label
	 * @param  string $title
	 * @dataProvider editActionData
	 */
	public function testEditAction($attachmentId, $label, $title)
	{
		$this->requestMock->expects($this->once())
			->method('getParam')
			->with('attachment_id')
			->willReturn($attachmentId);

		$this->objectManagerMock->expects($this->once())
			->method('create')
			->with(\Magezon\CustomerAttachments\Model\Attachment::class)
			->willReturn($this->attachmentMock);

		$this->attachmentMock->expects($this->any())
			->method('load')
			->with($attachmentId);
		$this->attachmentMock->expects($this->any())
			->method('getId')
			->willReturn($attachmentId);
		$this->attachmentMock->expects($this->any())
			->method('getName')
			->willReturn('Test Name');

		$this->coreRegistryMock->expects($this->once())
			->method('register')
			->with('customerattachments_attachment', $this->attachmentMock);

		$resultPageMock = $this->createMock(\Magento\Backend\Model\View\Result\Page::class);

		$this->resultPageFactoryMock->expects($this->once())
			->method('create')
			->willReturn($resultPageMock);

		$titleMock = $this->createMock(\Magento\Framework\View\Page\Title::class);
		$titleMock->expects($this->at(0))->method('prepend')->with(__('Attachments'));
		$titleMock->expects($this->at(1))->method('prepend')->with($this->getTitle());
		$pageConfigMock = $this->createMock(\Magento\Framework\View\Page\Config::class);
        $pageConfigMock->expects($this->exactly(2))->method('getTitle')->willReturn($titleMock);

		$resultPageMock->expects($this->once())
			->method('setActiveMenu')
			->willReturnSelf();
		$resultPageMock->expects($this->any())
			->method('addBreadcrumb')
			->willReturnSelf();
		$resultPageMock->expects($this->at(3))
			->method('addBreadcrumb')
			->with(__($label), __($title))
			->willReturnSelf();
		$resultPageMock->expects($this->exactly(2))
			->method('getConfig')
			->willReturn($pageConfigMock);

		$this->assertSame($resultPageMock, $this->editController->execute());
	}

    /**
     * @return \Magento\Framework\Phrase|string
     */
	public function getTitle()
	{
		return $this->attachmentMock->getId() ? $this->attachmentMock->getName() : __('New Attachment');
	}

	/**
	 * @return array
	 */
	public function editActionData()
	{
		return [
			[null, 'New Attachment', 'New Attachment'],
			[1, 'Edit Attachment', 'Edit Attachment']
		];
	}
}
