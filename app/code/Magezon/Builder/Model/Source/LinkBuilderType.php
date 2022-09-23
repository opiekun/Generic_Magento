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
 * @package   Magezon_Builder
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Builder\Model\Source;

class LinkBuilderType implements \Magento\Framework\Data\OptionSourceInterface
{
	/**
	 * @return array
	 */
	public function toOptionArray()
	{
		$options[] = [
			'label' => __('Category'),
			'value' => 'category'
		];
		$options[] = [
			'label' => __('Product'),
			'value' => 'product'
		];
		$options[] = [
			'label' => __('Page'),
			'value' => 'page'
		];
		$options[] = [
			'label' => __('Custom'),
			'value' => 'custom'
		];
		return $options;
	}
}