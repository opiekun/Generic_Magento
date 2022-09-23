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

class Button extends \Magezon\Builder\Block\Element
{
	/**
	 * @return string
	 */
	public function getAdditionalStyleHtml()
	{
		$styleHtml      = '';
		$element        = $this->getElement();
		$buttonSelector = '.mgz-link';

		// NORMAL STYLES
		$styles = [];
		if ($element->hasData('button_border_width')) {
			$styles['border-width'] = $this->getStyleProperty($element->getData('button_border_width'));
			$styles['border-style'] = $element->getData('button_border_style');
			$styles['border-color'] = $this->getStyleColor($element->getData('button_border_color'));
		}

		if ($element->hasData('button_border_radius')) {
			$styles['border-radius'] = $this->getStyleProperty($element->getData('button_border_radius'));
		}

		$styles['color']            = $this->getStyleColor($element->getData('button_color'));
		$styles['background-color'] = $this->getStyleColor($element->getData('button_background_color'));

		if ($element->getData('full_width') && ($element->getData('full_width') !== 'false')) {
			$styles['width'] = '100%';
		}

		if ($element->getData('button_style') == 'gradient') {
			$gradientColor1 = $this->getStyleColor($element->getData('gradient_color_1'));
			$gradientColor2 = $this->getStyleColor($element->getData('gradient_color_2'));
			$styles['background-color'] = $gradientColor1;
			$styles['background-image'] = 'linear-gradient(to right, ' . $gradientColor1 . ' 0%, ' . $gradientColor2 . ' 50%,' . $gradientColor1 . ' 100%)';
			$styles['background-size']  = '200% 100%';
		}
		$styleHtml .= $this->getStyles($buttonSelector, $styles);

		if ($element->getData('button_style') == '3d') {
			$styles['box-shadow'] = '0 5px 0 ' . $this->getStyleColor($element->getData('box_shadow_color'));
		}
		$styleHtml .= $this->getStyles($buttonSelector, $styles);


        // HOVER
        $styles = [];
		$styles['color']            = $this->getStyleColor($element->getData('button_hover_color'));
		$styles['background-color'] = $this->getStyleColor($element->getData('button_hover_background_color'));
		$styles['border-color']     = $this->getStyleColor($element->getData('button_hover_border_color'));
		if ($element->getData('button_style') == '3d') {
			$styles['box-shadow'] = '0 2px 0 ' . $this->getStyleColor($element->getData('box_shadow_color'));
		}

		$styleHtml .= $this->getStyles($buttonSelector, $styles, ':hover');


		// CUSTOM CSS
		if ($element->getData('button_css')) {
			$styleHtml .= '.mgz-element.' . $element->getHtmlId() . ' ' . $buttonSelector . '{';
				$styleHtml .= $element->getData('button_css');
			$styleHtml .= '}';
		}

		if ($element->getData('auto_width')) {
			$styleHtml .= '.' . $element->getHtmlId() . '{';
				$styleHtml .= 'width: auto;';
				$styleHtml .= 'display: inline-block;';
			$styleHtml .= '}';
		}

		return $styleHtml;
	}
}