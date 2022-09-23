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

class ResizableSizes
{
	/**
	 * @return array
	 */
	public function getOptions()
	{
		$options[] = [
			'label'      => '1 column - 1/12',
			'shortLabel' => '1/12',
			'value'      => 1,
			'percent'    => 8.33333333
		];

		$options[] = [
			'label'      => '2 columns - 1/6',
			'shortLabel' => '1/6',
			'value'      => 2,
			'percent'    => 16.66666667
		];

		$options[] = [
			'label'      => '3 columns - 1/4',
			'shortLabel' => '1/4',
			'value'      => 3,
			'percent'    => 25
		];

		$options[] = [
			'label'      => '4 columns - 1/3',
			'shortLabel' => '1/3',
			'value'      => 4,
			'percent'    => 33.33333333
		];

		$options[] = [
			'label'      => '5 columns - 5/12',
			'shortLabel' => '5/12',
			'value'      => 5,
			'percent'    => 41.66666667
		];

		$options[] = [
			'label'      => '6 columns - 1/2',
			'shortLabel' => '1/2',
			'value'      => 6,
			'percent'    => 50
		];

		$options[] = [
			'label'      => '7 columns - 7/12',
			'shortLabel' => '7/12',
			'value'      => 7,
			'percent'    => 58.33333333
		];

		$options[] = [
			'label'      => '8 columns - 2/3',
			'shortLabel' => '2/3',
			'value'      => 8,
			'percent'    => 66.66666667
		];

		$options[] = [
			'label'      => '9 columns - 3/4',
			'shortLabel' => '3/4',
			'value'      => 9,
			'percent'    => 75
		];

		$options[] = [
			'label'      => '10 columns - 5/6',
			'shortLabel' => '5/6',
			'value'      => 10,
			'percent'    => 83.33333333
		];

		$options[] = [
			'label'      => '11 columns - 11/12',
			'shortLabel' => '11/12',
			'value'      => 11,
			'percent'    => 91.66666667
		];

		$options[] = [
			'label'      => '12 columns - 1/1',
			'shortLabel' => '1/1',
			'value'      => 12,
			'percent'    => 100
		];

		$options[] = [
			'label'      => '20% - 1/5',
			'shortLabel' => '20%',
			'value'      => 15,
			'percent'    => 20
		];

		$options[] = [
			'label'      => '40% - 2/5',
			'shortLabel' => '40%',
			'value'      => 25,
			'percent'    => 40
		];

		$options[] = [
			'label'      => '60% - 3/5',
			'shortLabel' => '60%',
			'value'      => 35,
			'percent'    => 60
		];

		$options[] = [
			'label'      => '80% - 4/5',
			'shortLabel' => '80%',
			'value'      => 45,
			'percent'    => 80
		];

		return $options;
	}
}