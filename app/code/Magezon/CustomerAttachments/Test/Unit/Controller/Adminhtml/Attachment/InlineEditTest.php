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

use Magezon\CustomerAttachments\Controller\Adminhtml\Attachment\InlineEdit;

class InlineEditTest extends \PHPUnit\Framework\TestCase
{
	/** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
	protected $objectManager;

	/** @var \Magento\Framework\ObjectManager\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
	protected $objectManagerMock;

	/** @var  \Magezon\CustomerAttachments\Model\Attachment|\PHPUnit_Framework_MockObject_MockObject */
	protected $attachmentMock;

	/** @var  \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $requestMock;

	/** @var  \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $messageManagerMock;

	/** @var  \Magento\Framework\Controller\Result\Json|\PHPUnit_Framework_MockObject_MockObject */
	protected $resultJson;

	/** @var  \Magento\Backend\App\Action\Context */
	protected $context;

	/** @var  \Magento\Framework\Controller\Result\JsonFactory */
	protected $jsonFactory;

	protected function setUp()
	{
		$this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

		$this->objectManagerMock  = $this->getMockBuilder(\Magento\Framework\ObjectManager\ObjectManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

		$this->attachmentMock = $this->getMockBuilder(\Magezon\CustomerAttachments\Model\Attachment::class)
			->disableOriginalConstructor()
			->setMethods(['load', 'save', 'getId', 'getData', 'setData'])
			->getMock();

		$this->requestMock        = $this->getMockForAbstractClass('\Magento\Framework\App\RequestInterface');
		$this->messageManagerMock = $this->getMockForAbstractClass('\Magento\Framework\Message\ManagerInterface');

		$this->resultJson = $this->getMockBuilder(\Magento\Framework\Controller\Result\Json::class)
			->disableOriginalConstructor()
			->setMethods(['setData'])
			->getMock();

		$this->context = $this->objectManager->getObject(
			\Magento\Backend\App\Action\Context::class,
            [
				'request'        => $this->requestMock,
				'messageManager' => $this->messageManagerMock,
				'objectManager'  => $this->objectManagerMock
            ]
		);

        $this->jsonFactory = $this->getMockBuilder(\Magento\Framework\Controller\Result\JsonFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

		$this->controller = new InlineEdit(
			$this->context,
			$this->jsonFactory
		);
	}

	public function prepareMocksForTestExecute()
	{
		$postData = [
			1 => [
				'name'    => 'Homepage',
				'content' => 'Homepage'
			]
		];
		$this->requestMock->expects($this->any())
            ->method('getParam')
            ->willReturnMap(
                [
                    ['isAjax', null, true],
                    ['items', [], $postData]
                ]
            );

        $this->objectManagerMock->expects($this->once())
			->method('create')
			->with(\Magezon\CustomerAttachments\Model\Attachment::class)
			->willReturn($this->attachmentMock);
		$this->attachmentMock->expects($this->atLeastOnce())
			->method('load');
		$this->attachmentMock->expects($this->atLeastOnce())
			->method('getId')
			->willReturn(1);
		$this->attachmentMock->expects($this->atLeastOnce())
			->method('getData')
			->willReturn([
				'is_active' => 1
			]);
		$this->attachmentMock->expects($this->atLeastOnce())
			->method('setData')
			->with([
				'name'      => 'Homepage',
				'content'   => 'Homepage',
				'is_active' => 1
			]);

		$this->jsonFactory->expects($this->once())
			->method('create')
			->willReturn($this->resultJson);
	}

	public function testExecuteWithException()
	{
		$this->prepareMocksForTestExecute();
		$this->attachmentMock->expects($this->once())
			->method('save')
			->willThrowException(new \Exception(__('Exception')));
		$this->resultJson->expects($this->once())
            ->method('setData')
            ->with([
                'messages' => [
                    '[Attachment ID: 1] Something went wrong while saving the attachment.'
                ],
                'error' => true
            ])
            ->willReturnSelf();

		$this->assertSame($this->resultJson, $this->controller->execute());
	}

	public function testExecuteWithoutData()
	{
		$this->jsonFactory->expects($this->once())
			->method('create')
			->willReturn($this->resultJson);

		$this->requestMock->expects($this->any())
			->method('getParam')
			->willReturnMap(
				[
					['items', [], []],
					['isAjax', null, true]
				]
			);

		$this->resultJson->expects($this->atLeastOnce())
			->method('setData')
			->with([
				'messages' => [
                    __('Please correct the data sent.')
                ],
                'error' => true
			])
			->willReturnSelf();

		$this->assertSame($this->resultJson, $this->controller->execute());
	}

	public function testSetAttachmentData()
	{
		$extendedPageData = [
			'attachment_id' => '1',
			'name'      => 'Homepage',
			'store_id'  => ['0']
		];

		$attachmentData = [
			'attachment_id' => '1',
			'name'      => 'Homepage',
			'is_active' => '1'
		];

		$getData = [
			'attachment_id' => '1',
			'name'      => 'Homepage',
			'content'   => 'Homepage',
			'is_active' => '1',
			'store_id'  => ['0']
		];

		$mergeData = [
			'attachment_id' => '1',
			'name'      => 'Homepage',
			'content'   => 'Homepage',
			'is_active' => '1',
			'store_id'  => ['0']
		];

		$this->attachmentMock->expects($this->once())
			->method('getData')
			->willReturn($getData);
		$this->attachmentMock->expects($this->once())
			->method('setData')
			->with($mergeData)
			->willReturnSelf();
		$this->assertSame(
            $this->controller,
            $this->controller->setAttachmentData($this->attachmentMock, $extendedPageData, $attachmentData)
        );
	}
}
