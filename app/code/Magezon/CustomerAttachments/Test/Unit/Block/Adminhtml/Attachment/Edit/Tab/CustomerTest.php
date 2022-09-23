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

namespace Magezon\CustomerAttachments\Test\Unit\Block\Adminhtml\Attachment\Edit\Tab;

use Magezon\CustomerAttachments\Block\Adminhtml\Attachment\Edit\Button\Back;
use Magezon\CustomerAttachments\Block\Adminhtml\Attachment\Edit\Tab\Customer;
use \Magento\Customer\Model\ResourceModel\Customer\Collection;
use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class CustomerTest extends \PHPUnit\Framework\TestCase
{
	/** @var \Magento\Customer\Model\CustomerFactory|\PHPUnit_Framework_MockObject_MockObject */
	protected $customerFactory;

	/** @var Customer */
	protected $tab;

	protected function setUp()
	{
		$this->customerFactory = $this->getMockBuilder(\Magento\Customer\Model\CustomerFactory::class)
			->disableOriginalConstructor()
			->setMethods(['create', 'getCollection'])
			->getMock();

		$this->tab = $this->model = (new ObjectManager($this))->getObject(Customer::class, [
			'customerFactory' => $this->customerFactory
		]);
	}

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getCollectionMock()
    {
        return $this->createMock(\Magento\Customer\Model\ResourceModel\Customer\Collection::class);
    }

	public function testSetCollection()
	{
		$collection = $this->getCollectionMock();
		$this->tab->setCollection($collection);

		$this->assertSame($collection, $this->tab->getCollection());
	}
}
