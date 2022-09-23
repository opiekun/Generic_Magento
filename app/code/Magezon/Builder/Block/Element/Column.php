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

namespace Magezon\Builder\Block\Element;

class Column extends \Magezon\Builder\Block\Element
{
    /**
     * @return array
     */
	public function getWrapperClasses()
	{
		$classes = parent::getWrapperClasses();
		$element = $this->getElement();
		$parent  = $this->getParentElement();

		if ($parent) {
			$gapType = $parent->getData('gap_type') ? $parent->getData('gap_type') : 'margin';
			if ($gapType == 'margin') {
				$classes[] = 'mgz-row-gap-margin';
			}
		}

		$elements = $element->getData('elements');
		if (empty($elements)) {
			$classes[] = 'mgz-element-column-empty';
		}

		return $classes;
	}
}