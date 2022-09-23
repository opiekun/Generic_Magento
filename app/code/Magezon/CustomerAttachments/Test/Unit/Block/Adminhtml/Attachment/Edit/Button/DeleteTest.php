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

use Magezon\CustomerAttachments\Block\Adminhtml\Attachment\Edit\Button\Delete;

class DeleteTest extends Generic
{
	public function testGetButtonData()
	{
		$attachmentId  = 1;
		$deleteUrl = 'http://magezon.com/customerattachments/attachment/delete/' . $attachmentId;

		$this->attachmentMock->expects($this->atLeastOnce())
			->method('getId')
			->willReturn($attachmentId);

		$this->contextMock->expects($this->once())
			->method('getUrl')
			->willReturn($deleteUrl);

		$this->authorizationMock->expects($this->once())
			->method('isAllowed')
			->willReturn(true);

		$buttonData = [
                'label'    => __('Delete Attachment'),
                'class'    => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $deleteUrl . '\')',
                'sort_order' => 20,
            ];

		$this->assertEquals($buttonData, $this->getModel(Delete::class)->getButtonData());
	}

	public function testGetButtonDataWithoutAttachment()
	{
		$this->attachmentMock->expects($this->once())
			->method('getId')
			->willReturn(false);

		$this->contextMock->expects($this->never())
			->method('getUrl');

		$this->assertEquals([], $this->getModel(Delete::class)->getButtonData());
	}

	public function testGetButtonDataWithNotAllowed()
	{
		$this->attachmentMock->expects($this->once())
			->method('getId')
			->willReturn(true);

		$this->authorizationMock->expects($this->once())
			->method('isAllowed')
			->willReturn(false);

		$this->assertEquals([], $this->getModel(Delete::class)->getButtonData());
	}
}
