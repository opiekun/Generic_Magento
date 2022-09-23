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

class MassStatusTest extends \PHPUnit\Framework\TestCase
{
	/** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager */
	protected $objectManager;

	/** @var  \Magento\Ui\Component\MassAction\Filter*/
	protected $filterMock;

	/** @var  \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $requestMock;

	/** @var  \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $messageManagerMock;

	/** @var \Magento\Backend\Model\Session|\PHPUnit_Framework_MockObject_MockObject */
	protected $sessionMock;

	/** @var  \Magento\Framework\Controller\ResultFactory|\PHPUnit_Framework_MockObject_MockObject */
	protected $resultFactoryMock;

	/** @var  \Magento\Backend\Model\View\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject */
	protected $resultRedirectMock;

	/** @var  \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject */
	protected $contextMock;

	/** @var  \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject */
	protected $collectionFactoryMock;

	/** @var \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\Collection|\PHPUnit_Framework_MockObject_MockObject */
	protected $attachmentCollectionMock;

	/** @var \Magezon\CustomerAttachments\Controller\Adminhtml\Attachment\MassDelete */
	protected $massDeleteController;

	protected function setUp()
	{
		$this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

		$this->filterMock = $this->getMockBuilder(\Magento\Ui\Component\MassAction\Filter::class)
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

		$this->messageManagerMock = $this->createMock(\Magento\Framework\Message\ManagerInterface::class);

		$this->sessionMock = $this->getMockBuilder(\Magento\Backend\Model\Session::class)
			->disableOriginalConstructor()
			->getMock();

		$this->resultFactoryMock = $this->getMockBuilder(\Magento\Framework\Controller\ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultRedirectMock = $this->getMockBuilder(\Magento\Backend\Model\View\Result\Redirect::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultFactoryMock->expects($this->any())
            ->method('create')
            ->with(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT, [])
            ->willReturn($this->resultRedirectMock);

		$this->contextMock = $this->createMock(\Magento\Backend\App\Action\Context::class);
        $this->contextMock->expects($this->any())->method('getRequest')->willReturn($this->requestMock);
        $this->contextMock->expects($this->any())->method('getMessageManager')->willReturn($this->messageManagerMock);
        $this->contextMock->expects($this->any())->method('getResultFactory')->willReturn($this->resultFactoryMock);
        $this->contextMock->expects($this->any())->method('getSession')->willReturn($this->sessionMock);

        $this->collectionFactoryMock = $this->createPartialMock(
        	\Magezon\CustomerAttachments\Model\ResourceModel\Attachment\CollectionFactory::class,
        	['create']
        );

        $this->attachmentCollectionMock = $this->createMock(\Magezon\CustomerAttachments\Model\ResourceModel\Attachment\Collection::class);

        $this->massDeleteController = $this->objectManager->getObject(
        	\Magezon\CustomerAttachments\Controller\Adminhtml\Attachment\MassStatus::class,
        	[
				'context'           => $this->contextMock,
				'filter'            => $this->filterMock,
				'collectionFactory' => $this->collectionFactoryMock
        	]
        );
	}

	public function testMassStatusAction()
	{
		$status = 1;
		$updatedAttachmentCount = 2;

		$this->requestMock->expects($this->once())
			->method('getParam')
			->willReturn($status);

		$collection = [
			$this->getAttachmentMock($status),
			$this->getAttachmentMock($status)
		];

		$this->collectionFactoryMock->expects($this->once())
			->method('create')
			->willReturn($this->attachmentCollectionMock);

		$this->filterMock->expects($this->once())
			->method('getCollection')
			->with($this->attachmentCollectionMock)
			->willReturn($this->attachmentCollectionMock);

		$this->attachmentCollectionMock->expects($this->once())
			->method('getIterator')
			->willReturn(new \ArrayIterator($collection));

		$this->messageManagerMock->expects($this->once())
			->method('addSuccess')
			->with(__('A total of %1 record(s) have been updated.', $updatedAttachmentCount));
		$this->messageManagerMock->expects($this->never())->method('addError');

		$this->resultRedirectMock->expects($this->once())
			->method('setPath')
			->with('*/*/')
			->willReturnSelf();

		$this->assertSame($this->resultRedirectMock, $this->massDeleteController->execute());

	}

	/**
	 * Create attachment colleciton mock
	 * 
	 * @return \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\Collection|\PHPUnit_Framework_MockObject_MockObject
	 */
	protected function getAttachmentMock($status)
	{
		$attachmentMock = $this->createPartialMock(\Magezon\CustomerAttachments\Model\ResourceModel\Attachment\CollectionFactory::class, ['setIsActive', 'save']);
		$attachmentMock->expects($this->once())
			->method('setIsActive')
			->with($status)
			->willReturn(true);
		$attachmentMock->expects($this->once())
			->method('save')
			->willReturn(true);

		return $attachmentMock;
	}
}
