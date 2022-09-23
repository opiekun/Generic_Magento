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

namespace Magezon\CustomerAttachments\Block\Adminhtml;

class TopMenu extends \Magezon\Core\Block\Adminhtml\TopMenu
{
	/**
	 * Init menu items
	 * 
	 * @return array
	 */
	public function intLinks()
	{
		$links = [
			[
				[
					'title'    => __('Add New Attachment'),
					'link'     => $this->getUrl('*/*/new'),
					'resource' => 'Magezon_CustomerAttachments::attachment_save'
				],
				[
					'title'    => __('Manage Attachments'),
					'link'     => $this->getUrl('*/*'),
					'resource' => 'Magezon_CustomerAttachments::attachment'
				],
				[
					'title'    => __('Settings'),
					'link'     => $this->getUrl('adminhtml/system_config/edit/section/customerattachments'),
					'resource' => 'Magezon_CustomerAttachments::settings'
				]
			],
			[
				'class' => 'separator'
			],
			[
				'title'  => __('User Guide'),
				'link'   => 'https://magezon.com/customer-attachments.html',
				'target' => '_blank'
			],
			[
				'title'  => __('Change Log'),
				'link'   => 'https://magezon.com/customer-attachments.html',
				'target' => '_blank'
			],
			[
				'title'  => __('Get Support'),
				'link'   => $this->getSupportLink(),
				'target' => '_blank'
			]
		];
		return $links;
	}
}
