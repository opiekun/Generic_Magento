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

class AnimateIn
{
	/**
	 * @return array
	 */
	public function getOptions()
	{
		$groups = [];
		$groups[] = [
			'label'    => 'Attention Seekers',
			'children' => [
				'bounce',
				'flash',
				'pulse',
				'rubberBand',
				'shake',
				'swing',
				'tada',
				'wobble',
				'jello',
			]
		];

		$groups[] = [
			'label'    => 'Bouncing Entrances',
			'children' => [
				'bounceIn',
				'bounceInDown',
				'bounceInLeft',
				'bounceInRight',
				'bounceInUp'
			]
		];

		$groups[] = [
			'label'    => 'Fading Entrances',
			'children' => [
				'fadeIn',
				'fadeInDown',
				'fadeInDownBig',
				'fadeInLeft',
				'fadeInLeftBig',
				'fadeInRight',
				'fadeInRightBig',
				'fadeInUp',
				'fadeInUpBig',
			]
		];

		$groups[] = [
			'label'    => 'Flippers',
			'children' => [
				'flipInX',
				'flipInY'
			]
		];

		$groups[] = [
			'label'    => 'Lightspeed',
			'children' => [
				'lightSpeedIn'
			]
		];

		$groups[] = [
			'label'    => 'Rotating Entrances',
			'children' => [
				'rotateIn',
				'rotateInDownLeft',
				'rotateInDownRight',
				'rotateInUpLeft',
				'rotateInUpRight'
			]
		];

		$groups[] = [
			'label'    => 'Rotating Entrances',
			'children' => [
				'rotateIn',
				'rotateInDownLeft',
				'rotateInDownRight',
				'rotateInUpLeft',
				'rotateInUpRight'
			]
		];

		$groups[] = [
			'label'    => 'Specials',
			'children' => [
				'rollIn'
			]
		];

		$groups[] = [
			'label'    => 'Zoom Entrances',
			'children' => [
				'zoomIn',
				'zoomInDown',
				'zoomInLeft',
				'zoomInRight',
				'zoomInUp'
			]
		];

		$groups[] = [
			'label'    => 'Slide Entrances',
			'children' => [
				'slideInDown',
				'slideInLeft',
				'slideInRight',
				'slideInUp',
			]
		];

		$options[] = [
			'label' => 'None'
		];
		foreach ($groups as $group) {
			foreach ($group['children'] as $k => $v) {
				$options[] = [
					'label' => $v,
					'value' => $v,
					'group' => $group['label']
				];
			}
		}

		$options[] = [
			'label' => 'Top to Bottom',
			'value' => 'mgz_top-to-bottom',
			'group' => 'Custom'
		];

		$options[] = [
			'label' => 'Bottom to Top',
			'value' => 'mgz_bottom-to-top',
			'group' => 'Custom'
		];

		$options[] = [
			'label' => 'Left to Right',
			'value' => 'mgz_left-to-right',
			'group' => 'Custom'
		];

		$options[] = [
			'label' => 'Right to left',
			'value' => 'mgz_right-to-left',
			'group' => 'Custom'
		];

		$options[] = [
			'label' => 'Appear from center',
			'value' => 'mgz_appear',
			'group' => 'Custom'
		];

		$options[] = [
			'label' => 'backSlideIn',
			'value' => 'owl-backSlide-in',
			'group' => 'Custom'
		];

		$options[] = [
			'label' => 'fadeUpIn',
			'value' => 'owl-fadeUp-in',
			'group' => 'Custom'
		];

		$options[] = [
			'label' => 'goDownIn',
			'value' => 'owl-goDown-in',
			'group' => 'Custom'
		];

		return $options;
	}
}