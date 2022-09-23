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

namespace Magezon\CustomerAttachments\Block\Account\Renderer\Attachment;

class RemainingDownloads extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
	/**
	 * @param  \Magento\Framework\DataObject $row
	 * @return string
	 */
	public function _getValue(\Magento\Framework\DataObject $row)
	{
		if ($numberOfDownloads = (int)$row->getNumberOfDownloads()) {
			$downloads = $numberOfDownloads - $row->getNumberOfDownloadsUsed();
			return '<span>' . $downloads . '</span>';
		}
		return __('Unlimited');
	}
}
