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

namespace Magezon\Builder\Block\Element;

class Countdown extends \Magezon\Builder\Block\Element
{
	public function getTime()
	{
		$element  = $this->getElement();
		$year     = (int)$element->getData('year');
		$month    = (int)$element->getData('month');
		$day      = (int)$element->getData('day');
		$hours    = (int)$element->getData('hours');
		$minutes  = $element->getData('minutes');
		$str      = $year . '-' . $month . '-' . $day . ' ' . $hours . ':' . $minutes . ':00';
		$timezone = $element->getData('time_zone') ? $element->getData('time_zone') : 'UTC';
		$date     = new \DateTime($str, new \DateTimeZone($timezone));
		return $date->format(\DateTime::ATOM);
	}

	/**
	 * @return string
	 */
	public function getAdditionalStyleHtml()
	{
		$styleHtml     = '';
		$element       = $this->getElement();
		$layout        = $element->getData('layout');
		$numberSpacing = $element->getData('number_spacing');
		$separatorType = $element->getData('separator_type');
	
		$styles              = [];
		$styles['color']     = $this->getStyleColor($element->getData('number_color'));
		$styles['font-size'] = $this->getStyleProperty($element->getData('number_size'));
		$styleHtml .= $this->getStyles('.mgz-countdown-unit-number', $styles);
	
		$styles              = [];
		$styles['color']     = $this->getStyleColor($element->getData('text_color'));
		$styles['font-size'] = $this->getStyleProperty($element->getData('text_size'));
		$styleHtml .= $this->getStyles('.mgz-countdown-unit-label', $styles);
	
		$styles              = [];
		$styles['color']     = $this->getStyleColor($element->getData('heading_color'));
		$styles['font-size'] = $this->getStyleProperty($element->getData('heading_font_size'));
		$styleHtml .= $this->getStyles('.mgz-countdown-heading', $styles);
	
		$styles              = [];
		$styles['color']     = $this->getStyleColor($element->getData('sub_heading_color'));
		$styles['font-size'] = $this->getStyleProperty($element->getData('sub_heading_font_size'));
		$styleHtml .= $this->getStyles('.mgz-countdown-subheading', $styles);
	
		$styles              = [];
		$styles['color']     = $this->getStyleColor($element->getData('link_color'));
		$styles['font-size'] = $this->getStyleProperty($element->getData('link_font_size'));
		$styleHtml .= $this->getStyles('.mgz-countdown-link', $styles);
	
		$styles                     = [];
		$styles['background-color'] = $this->getStyleColor($element->getData('number_background_color'));
		$styles['border-radius']    = $this->getStyleProperty($element->getData('number_border_radius'));
		$styles['padding']          = $element->getData('number_padding')!='' ? $this->getStyleProperty($element->getData('number_padding')) : '';
		$styleHtml .= $this->getStyles('.mgz-countdown-unit', $styles);

		$styles = [];
		$styles['margin'] = $this->getStyleProperty($numberSpacing);
		$styleHtml .= $this->getStyles('.mgz-countdown-number', $styles);

		if ($element->getData('show_separator')) {
			$styles = [];

			if ($separatorType == 'colon') {
				$styles['color']     = $this->getStyleColor($element->getData('separator_color'));
				$styles['width']     = $this->getStyleProperty((int)$numberSpacing * 2);
				$styles['right']     = $this->getStyleProperty((int)$numberSpacing * 2);
				$styles['font-size'] = $this->getStyleProperty($element->getData('separator_size'));
			}

			if ($separatorType == 'line') {
				$styles['border-color'] = $this->getStyleColor($element->getData('separator_color'));
				$styles['right']        = $this->getStyleProperty($numberSpacing);
				$styles['height']       = $this->getStyleProperty($element->getData('separator_size'));
			}

			if ($layout == 'circle') {
				$styles['right'] = '-10px';
			} else {
				if (isset($styles['right']) && $styles['right']) {
					$styles['right'] = '-' . $styles['right'];
				} else {
					$styles['right'] = '-2px';
				}

			}
			$styleHtml .= $this->getStyles('.mgz-countdown-number::after', $styles);
		}

		if ($layout == 'circle') {
			$styles           = [];
			$styles['width']  = $this->getStyleProperty($element->getData('circle_size'));
			$styles['height'] = $this->getStyleProperty($element->getData('circle_size'));
			$styleHtml .= $this->getStyles('.mgz-countdown-circle-container', $styles);

			$styles = [];
			$styles['stroke-width'] = $this->getStyleProperty($element->getData('circle_dash_width'));
			$styleHtml .= $this->getStyles('circle', $styles);

			$styles = [];
			$styles['stroke'] = $this->getStyleColor($element->getData('circle_color2'));
			$styleHtml .= $this->getStyles('.svg .mgz-element-bar-bg', $styles);

			$styles = [];
			$styles['stroke'] = $this->getStyleColor($element->getData('circle_color1'));
			$styleHtml .= $this->getStyles('.svg .mgz-element-bar', $styles);
		}

		return $styleHtml;
	}
}