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

namespace Magezon\CustomerAttachments\Test\Unit\Controller\Adminhtml\Attachment\File;

use Magezon\CustomerAttachments\Controller\Adminhtml\Attachment\File\Upload as AttachmentUploadController;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\DataObject;
use Magento\Framework\App\Request\Http as Request;
use Magento\Framework\App\Filesystem\DirectoryList;

class UploadTest extends \PHPUnit\Framework\TestCase
{
	/** @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager */
	protected $objectManager;

	/** @var ImageUploader|\PHPUnit_Framework_MockObject_MockObject */
	protected $fileUploaderMock;

	/** @var \Magento\Framework\Controller\ResultFactory|\PHPUnit_Framework_MockObject_MockObject */
	protected $resultFactoryMock;

	/** @var Request\PHPUnit_Framework_MockObject_MockObject */
	protected $requestMock;

	/** @var AttachmentUploadController|\PHPUnit_Framework_MockObject_MockObject */
	protected $controller;

	protected function setUp()
	{
		$this->objectManager    = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
		$this->fileUploaderMock = $this->getMockBuilder(\Magento\MediaStorage\Model\File\Uploader::class)
			->disableOriginalConstructor()
			->getMock();

		$this->requestMock = $this->objectManager->getObject(Request::class);

		$this->objectManagerMock = $this->getMockBuilder(\Magento\Framework\ObjectManager\ObjectManager::class)
            ->setMethods(['create', 'get'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultRawMock = $this->getMockBuilder(\Magento\Framework\Controller\Result\Raw::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultRawFactoryMock = $this->getMockBuilder(\Magento\Framework\Controller\Result\RawFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->fileHelperMock = $this->createMock(\Magezon\CustomerAttachments\Helper\File::class);

		$this->controller = (new ObjectManager($this))->getObject(AttachmentUploadController::class, [
			'request'          => $this->requestMock,
			'fileUploader'     => $this->fileUploaderMock,
			'resultRawFactory' => $this->resultRawFactoryMock,
			'fileHelper'       => $this->fileHelperMock,
			'objectManager'    => $this->objectManagerMock,
		]);
	}

	public function testExecute()
	{
		$input = [
			'file' => 'demo.jpg'
		];

		$output = [
			'file' => 'demo.jpg.tmp',
			'url'  => 'https://magento.com/demo.jpg'
		];

		$this->requestMock->setParam('param_name', $input['file']);
		$fileSystem = $this->createMock(\Magento\Framework\Filesystem::class);
		$this->objectManagerMock->expects($this->once())
			->method('get')
			->with(\Magento\Framework\Filesystem::class)
			->willReturn($fileSystem);

		$mediaDirectory = $this->createMock(\Magento\Framework\Filesystem\Directory\Read::class);
		$fileSystem->expects($this->once())
			->method('getDirectoryRead')
			->with(DirectoryList::MEDIA)
			->willReturn($mediaDirectory);
		$mediaDirectory->expects($this->once())
			->method('getAbsolutePath')
			->willReturn('customerattachments/attachment');

		$uploader = $this->createMock(\Magento\MediaStorage\Model\File\Uploader::class);
		$this->objectManagerMock->expects($this->once())
			->method('create')
			->with(\Magento\MediaStorage\Model\File\Uploader::class, ['fileId' => $input['file']])
			->willReturn($uploader);
		$uploader->expects($this->once())->method('setAllowRenameFiles')->with(true)->willReturnSelf();
		$uploader->expects($this->once())->method('setFilesDispersion')->with(true)->willReturnSelf();
		$uploader->expects($this->once())->method('save')->willReturn($input);

		$this->fileHelperMock->expects($this->exactly(1))
			->method('getTmpMediaUrl')
			->with($input['file'])
			->willReturn($output['url']);
		
		$this->resultRawMock->expects($this->once())
            ->method('setContents')
            ->with(json_encode($output));
		$this->resultRawFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->resultRawMock);

		$this->controller->execute();
	}
}
