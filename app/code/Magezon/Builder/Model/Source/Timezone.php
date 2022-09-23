<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_PageBuilder
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Builder\Model\Source;

class Timezone extends \Magento\Config\Model\Config\Source\Locale\Timezone
{
	/**
	 * @return array
	 */
	public function getConfig()
	{
		$options = $this->toOptionArray();
		array_unshift($options, [
			'label' => 'UTC',
			'value' => 'UTC'
		]);
		return $options;
	}
}