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

class Tabs extends \Magezon\Builder\Block\Element
{
	/**
	 * @return string
	 */
	public function getAdditionalStyleHtml()
	{
		$styleHtml         = '';
		$element           = $this->getElement();
		$id                = $element->getId();
		$titleSelector     = '.mgz-tabs-' . $id . ' > .mgz-tabs-nav > .mgz-tabs-tab-title';
		$titleSelector2    = '.mgz-tabs-' . $id . ' > .mgz-tabs-content > .mgz-tabs-tab-title';
		$contentSelector   = '.mgz-tabs-' . $id . ' > .mgz-tabs-content > .mgz-tabs-tab-content';
		$noFillContentArea = $element->getData('no_fill_content_area');
		$tabPosition       = $element->getData('tab_position');

		// TABS CONTENT
		$styles = [];
		$styles['border-color'] = $this->getStyleColor($element->getData('tab_border_color'));
		$styles['border-width'] = $this->getStyleProperty($element->getData('tab_border_width'));
		if (!$noFillContentArea) {
			if ($element->hasData('tab_content_background_color')) {
				$styles['background'] = $this->getStyleColor($element->getData('tab_content_background_color'));
			} else {
				// OLD VERSION
				$styles['background'] = $this->getStyleColor($element->getData('tab_background_color'));
			}
		}
		if ($element->hasData('tab_border_radius')) {
			$styles['border-radius'] = $this->getStyleProperty($element->getData('tab_border_radius'));
		}
		$border = (int) $element->getData('tab_border_width');
		if ($border) {
			switch ($tabPosition) {
				case 'top':
					$styles['margin-top'] = '-' . $this->getStyleProperty($border, true);
					break;

				case 'right':
					$styles['margin-right'] = '-' . $this->getStyleProperty($border, true);
					break;

				case 'bottom':
					$styles['margin-bottom'] = '-' . $this->getStyleProperty($border, true);
					break;

				case 'left':
					$styles['margin-left'] = '-' . $this->getStyleProperty($border, true);
					break;
			}
		} else if ($element->getData('tab_border_width') != '') {
			$styles['margin'] = '0';
		}
		$styleHtml .= $this->getStyles($contentSelector, $styles);

		// NORMAL
		$styles = [];
		if ($element->hasData('title_font_size')) {
			$styles['font-size'] = $this->getStyleProperty($element->getData('title_font_size'));
		}
		$styleHtml .= $this->getStyles([
			$titleSelector . ' > a',
			$titleSelector2 . ' > a'
		], $styles);

		$styles = [];
		if ($element->hasData('tab_border_width')) {
			$styles['border-width'] = $this->getStyleProperty($element->getData('tab_border_width'));
		}

		if ($element->hasData('tab_border_radius')) {
			$styles['border-radius'] = $this->getStyleProperty($element->getData('tab_border_radius'));
		}

		if ($element->getData('tab_border_style')) {
			$styles['border-style'] = $element->getData('tab_border_style');
		}

		$tabBorderWidth = $this->getStyleProperty($element->getData('tab_border_width'), true);
		if ($tabBorderWidth) {
			// Apply both Desktop & Mobile
			//$styles['margin-top'] = '-' . $tabBorderWidth;
			switch ($tabPosition) {

				case 'top':
					$styles['margin-top'] = '-' . $tabBorderWidth;
					break;

				case 'right':
					$styles['margin-right'] = '-' . $tabBorderWidth;
					break;

				case 'bottom':
					$styles['margin-bottom'] = '-' . $tabBorderWidth;
					break;

				case 'left':
					$styles['margin-left'] = '-' . $tabBorderWidth;
					break;
			}
		}
		
		$styles['color']        = $this->getStyleColor($element->getData('tab_color'));
		$styles['background']   = $this->getStyleColor($element->getData('tab_background_color'));
		$styles['border-color'] = $this->getStyleColor($element->getData('tab_border_color'));
		$styleHtml .= $this->getStyles([
			$titleSelector . ' > a',
			$titleSelector2 . ' > a'
		], $styles);


        // HOVER
		$styles = [];
		$styles['color']        = $this->getStyleColor($element->getData('tab_hover_color'));
		$styles['background']   = $this->getStyleColor($element->getData('tab_hover_background_color'));
		$styles['border-color'] = $this->getStyleColor($element->getData('tab_hover_border_color'));
		$styleHtml .= $this->getStyles([
			$titleSelector . ' > a'
		], $styles, ':hover');


        // ACTIVE
		$styles = [];
		$styles['color']        = $this->getStyleColor($element->getData('tab_active_color'));
		$styles['background']   = $this->getStyleColor($element->getData('tab_active_background_color'));
		$styles['border-color'] = $this->getStyleColor($element->getData('tab_active_border_color'));
		$styleHtml .= $this->getStyles([
			$titleSelector . '.mgz-active > a',
			$titleSelector2 . '.mgz-active > a'
		], $styles);


		if ($element->hasData('spacing')) {
			$styles                 = [];
			$spacing                = $this->getStyleProperty($element->getData('spacing'));
			$styles['margin-right'] = $spacing;
			$styles['margin-top']   = $spacing;
			$styleHtml .= $this->getStyles([
				$titleSelector . ' > a',
				$titleSelector2 . ' > a'
			], $styles);
		}

		if ($element->hasData('gap')) {
			$styles = [];
			$gap    = $this->getStyleProperty($element->getData('gap'));
			$styles['margin-top'] = $gap;

			switch ($tabPosition) {
				case 'top':
					$styles['margin-top'] = $gap;
					break;

				case 'right':
					$styles['margin-right'] = $gap;
					break;

				case 'bottom':
					$styles['margin-bottom'] = $gap;
					break;

				case 'left':
					$styles['margin-left'] = $gap;
					break;
			}
			$styleHtml .= $this->getStyles($contentSelector, $styles);
		}

		return $styleHtml;
	}
}