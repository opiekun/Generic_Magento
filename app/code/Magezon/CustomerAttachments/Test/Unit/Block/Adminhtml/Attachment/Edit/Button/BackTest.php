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

use Magezon\CustomerAttachments\Block\Adminhtml\Attachment\Edit\Button\Back;

class BackTest extends Generic
{
	public function testGetButtonData()
	{
		$this->contextMock->expects($this->once())
			->method('getUrl')
			->with('*/*/', [])
			->willReturn('/');

		$buttonData = [
            'label'      => __('Back'),
            'on_click'   => sprintf("location.href = '%s';", '/'),
            'class'      => 'back',
            'sort_order' => 10
        ];

        $this->assertEquals($buttonData, $this->getModel(Back::class)->getButtonData());
	}
}
