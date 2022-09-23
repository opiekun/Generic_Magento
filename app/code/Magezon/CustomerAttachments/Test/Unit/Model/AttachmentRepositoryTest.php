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

namespace Magezon\CustomerAttachments\Test\Unit\Model;

use Magezon\CustomerAttachments\Model\AttachmentRepository;

class AttachmentRepositoryTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * @var Magezon\CustomerAttachments\Model\AttachmentRepository
	 */
	protected $repository;

	/** @var \Magezon\CustomerAttachments\Model\ResourceModel\Attachment|\PHPUnit_Framework_MockObject_MockObject */
	protected $resourceMock;

	/** @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $collectionProcessor;

	/** @var \Magezon\CustomerAttachments\Model\Attachment|\PHPUnit_Framework_MockObject_MockObject */
	protected $attachmentMock;

	/** @var \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\Collection|\PHPUnit_Framework_MockObject_MockObject */
	protected $attachmentCollection;

	/** @var \Magezon\CustomerAttachments\Api\Data\AttachmentSearchResultsInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $attachmentSearchResult;

	protected function setUp()
	{
		$this->resourceMock = $this->getMockBuilder(\Magezon\CustomerAttachments\Model\ResourceModel\Attachment::class)
			->disableOriginalConstructor()
			->getMock();
		$attachmentFactory = $this->getMockBuilder(\Magezon\CustomerAttachments\Model\AttachmentFactory::class)
			->disableOriginalConstructor()
			->setMethods(['create'])
			->getMock();
		$attachmentCollectionFactory = $this->getMockBuilder(\Magezon\CustomerAttachments\Model\ResourceModel\Attachment\CollectionFactory::class)
			->disableOriginalConstructor()
			->setMethods(['create'])
			->getMock();
		$attachmentSearchResultFactory = $this->getMockBuilder(\Magezon\CustomerAttachments\Api\Data\AttachmentSearchResultsInterfaceFactory::class)
			->disableOriginalConstructor()
			->setMethods(['create'])
			->getMock();
		$this->collectionProcessor = $this->getMockBuilder(\Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface::class)
			->getMockForAbstractClass();

		$this->attachmentMock = $this->getMockBuilder(\Magezon\CustomerAttachments\Model\Attachment::class)
			->disableOriginalConstructor()
			->getMock();
		$attachmentFactory->expects($this->any())
			->method('create')
			->willReturn($this->attachmentMock);

		$this->attachmentCollection = $this->getMockBuilder(\Magezon\CustomerAttachments\Model\ResourceModel\Attachment\Collection::class)
			->disableOriginalConstructor()
			->setMethods(['getSize', 'setCurPage', 'setPageSize', 'load', 'addOrder'])
			->getMock();
		$attachmentCollectionFactory->expects($this->any())
			->method('create')
			->willReturn($this->attachmentCollection);

		$this->attachmentSearchResult = $this->getMockBuilder(\Magezon\CustomerAttachments\Api\Data\AttachmentSearchResultsInterface::class)
			->getMock();
		$attachmentSearchResultFactory->expects($this->any())
			->method('create')
			->willReturn($this->attachmentSearchResult);

		$this->repository = new AttachmentRepository(
			$this->resourceMock,
			$attachmentFactory,
			$attachmentCollectionFactory,
			$attachmentSearchResultFactory,
			$this->collectionProcessor
		);
	}

    /**
     * @test
     */
	public function testSave()
	{
		$this->resourceMock->expects($this->once())
			->method('save')
			->with($this->attachmentMock)
			->willReturnSelf();
		$this->assertEquals($this->attachmentMock, $this->repository->save($this->attachmentMock));
	}

    /**
     * @test
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     */
	public function testSaveWithException()
	{
		$this->resourceMock->expects($this->once())
			->method('save')
			->with($this->attachmentMock)
			->willThrowException(new \Exception());

		$this->repository->save($this->attachmentMock);
	}

    /**
     * @test
     */
	public function testGetById()
	{
		$attachmentId = 1;
		$this->attachmentMock->expects($this->once())
			->method('load')
			->willReturnSelf();
		$this->attachmentMock->expects($this->once())
			->method('getId')
			->willReturn($attachmentId);
		$this->assertEquals($this->attachmentMock, $this->repository->getById($attachmentId));
	}

	/**
	 * @test
	 * 
	 * @expectedException \Magento\Framework\Exception\LocalizedException
	 */
	public function testGetByIdWithException()
	{
		$attachmentId = 1;
		$this->attachmentMock->expects($this->once())
			->method('load')
			->with($attachmentId)
			->willReturnSelf();
		$this->attachmentMock->expects($this->once())
			->method('getId')
			->willReturn(false);
		$this->repository->getById($attachmentId);
	}

    /**
     * @test
     */
	public function testGetList()
	{
		$total = 10;

		/** @var \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria */
		$searchCriteria = $this->getMockBuilder(\Magento\Framework\Api\SearchCriteriaInterface::class)->getMock();

		$this->attachmentCollection->addItem($this->attachmentMock);
		$this->attachmentCollection->expects($this->once())
			->method('getSize')
			->willReturn($total);

		$this->collectionProcessor->expects($this->once())
			->method('process')
			->with($searchCriteria, $this->attachmentCollection)
			->willReturnSelf();

		$this->attachmentSearchResult->expects($this->once())
			->method('setSearchCriteria')
			->with($searchCriteria)
			->willReturnSelf();
		$this->attachmentSearchResult->expects($this->once())
			->method('setTotalCount')
			->with($total)
			->willReturnSelf();
		$this->attachmentSearchResult->expects($this->once())
			->method('setItems')
			->with([$this->attachmentMock])
			->willReturnSelf();
		$this->assertEquals($this->attachmentSearchResult, $this->repository->getList($searchCriteria));
	}

    /**
     * @test
     */
    public function testDelete()
    {
    	$this->resourceMock->expects($this->once())
    		->method('delete')
    		->with($this->attachmentMock)
    		->willReturn(true);
    	$this->assertTrue($this->repository->delete($this->attachmentMock));
    }

    /**
     * @test
     * 
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function testDeleteException()
    {
    	$this->resourceMock->expects($this->once())
    		->method('delete')
    		->with($this->attachmentMock)
    		->willThrowException(new \Exception());
    	$this->repository->delete($this->attachmentMock);
    }

    /**
     * @test
     */
    public function testDeleteById()
    {
    	$attachmentId = 1;
    	$this->attachmentMock->expects($this->once())
    		->method('load')
    		->with($attachmentId)
    		->willReturnSelf();
    	$this->attachmentMock->expects($this->once())
    		->method('getId')
    		->willReturn(true);
    	$this->resourceMock->expects($this->once())
    		->method('delete')
    		->with($this->attachmentMock)
    		->willReturnSelf();

    	$this->assertTrue($this->repository->deleteById($attachmentId));
    }
}
