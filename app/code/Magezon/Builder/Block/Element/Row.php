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

class Row extends \Magezon\Builder\Block\Element
{
    /**
     * @return array
     */
	public function getWrapperClasses()
	{
		$classes = parent::getWrapperClasses();

		$element = $this->getElement();
		$classes[] = $element->getData('row_type');

		if ($element->getData('row_type') == 'contained') {
			$classes[] = 'mgz-container';
		}

		if ($element->getData('equal_height')) {
			$classes[] = 'mgz-row-equal-height';

			if ($element->getData('content_position')) {
				$classes[] = 'content-' . $element->getData('content_position');
			}
		}

		if ($element->getData('full_height')) {
			$classes[] = 'mgz-row-full-height';
		}

		if ($element->getData('reverse_column')) {
			$classes[] = 'mgz-row-wrap-reverse';
		}

		return $classes;
	}

	/**
	 * @return string
	 */
	public function getAdditionalStyleHtml()
	{
		$styleHtml = '';
		$element   = $this->getElement();
		$elements  = $this->getElements();
		$count     = count($elements);
		if ($count) {
			if ($this->isNull($element->getData('gap'))) {
				if ($element->getData('row_type') == 'full_width_row_content_no_paddings') {
					$childSelector = '';
					foreach ($elements as $i => $_element) {
						$childSelector .= '.' . $_element->getHtmlId() . '>.mgz-element-inner';
						if (isset($elements[$i+1])) {
							$childSelector .= ',';
						}
					}
					$styleHtml .= $childSelector . '{';
						$styleHtml .= 'padding:0;margin:0;';
					$styleHtml .= '}';
				}
			} else {
				$gap           = (int) $element->getGap();
				$equalHeight   = $element->getData('equal_height');
				$gapType       = $element->getData('gap_type') ? $element->getData('gap_type') : 'margin';
				$childSelector = '';
				if ($equalHeight) {
					foreach ($elements as $i => $_element) {
						$childSelector .= '.' . $_element->getHtmlId();
						if (isset($elements[$i+1])) {
							$childSelector .= ',';
						}
					}
					$styleHtml .= $childSelector . '{';
						$styleHtml .= 'border:' . ($gap/2) . 'px solid transparent;';
					$styleHtml .= '}';
					$childSelector = '';
					foreach ($elements as $i => $_element) {
						$childSelector .= '.' . $_element->getHtmlId() . '>.mgz-element-inner';
						if (isset($elements[$i+1])) {
							$childSelector .= ',';
						}
					}
					$styleHtml .= $childSelector . '{';
						$styleHtml .= 'padding:0;margin:0;';
					$styleHtml .= '}';
				} else {
					foreach ($elements as $i => $_element) {
						$childSelector .= '.' . $_element->getHtmlId() . '>.mgz-element-inner';
						if (isset($elements[$i+1])) {
							$childSelector .= ',';
						}
					}
					$styleHtml .= $childSelector . '{';
						$styleHtml .= $gapType . ':' . ($gap/2) . 'px;';
					$styleHtml .= '}';
				}
			}
		}

		$styles = [];
		if ($element->getData('max_width')) {
			$styles['width'] = $this->getStyleProperty($element->getData('max_width'));
			$styles['max-width'] = '100%';
			$contentAlign = $element->getData('content_align');
			switch ($contentAlign) {
				case 'left':
					$styles['margin-left'] = '0';
					break;

				case 'center':
					$styles['margin'] = '0 auto';
					break;

				case 'right':
					$styles['margin-right'] = '0';
					break;
			}
		}
		$styleHtml .= $this->getStyles('>.mgz-element-inner>.inner-content', $styles);

		return $styleHtml;
	}
}