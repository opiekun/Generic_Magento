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

namespace Magezon\Builder\Block;

use \Magento\Framework\App\ObjectManager;

class ElementStyle extends \Magento\Framework\View\Element\Template
{
	public function getStylesHtml()
	{
		$dataHelper = $this->getDataHelper();
		$element    = $this->getElement();
		$deviceType = $element->getData('device_type');
		$html       = '';
		$prefixes   = [''];
		if ($deviceType == 'custom') $prefixes = ['', 'lg_', 'md_', 'sm_', 'xs_'];

		$styles = [];
		$styles['z-index'] = $element->getData('z_index');
		if ($element->getData('animation_duration')) $styles['animation-duration'] = $element->getData('animation_duration') . 's';

		$_html = $dataHelper->parseStyles($styles);
		if ($_html) {
			$html .= '.' . $element->getHtmlId() . '{';
			$html .= $_html;
			$html .= '}';
		}

		foreach ($prefixes as $_k => $_prefix) {
			// ELEMENT
			$styles = [];
			if ($deviceType == 'custom' || $element->getData($_prefix . 'el_float')) {
				$styles['float'] = $element->getData($_prefix . 'el_float');
			}
			$_html = $dataHelper->parseStyles($styles);
			if ($_html) {
				if ($deviceType == 'custom') {
					switch ($_prefix) {
						case 'xs_':
							$html .= '@media (max-width: 575px) {';
							break;

						case 'sm_':
							$html .= '@media (max-width: 767px) {';
							break;

						case 'md_':
							$html .= '@media (max-width: 991px) {';
							break;

						case 'lg_':
							$html .= '@media (max-width: 1199px) {';
							break;

						// Defualt xl
						default:
							//$html .= '@media (min-width: 1200px) {';
							break;
					}
				}
				$html .= '.' . $element->getHtmlId() . '{';
				$html .= $_html;
				$html .= '}';
				if ($deviceType == 'custom' && $_prefix) {
					$html .= '}';
				}
			}

			// INNER
			$styles = [];
			if ($deviceType == 'custom' || $element->getData($_prefix . 'align') != 'left' ) {
				$styles['text-align'] = $element->getData($_prefix . 'align');
			}
			$styles['min-height']     = $dataHelper->getStyleProperty($element->getData($_prefix . 'min_height'), true);

			$paddingTop    = $dataHelper->getStyleProperty($element->getData($_prefix . 'padding_top'));
			$paddingRight  = $dataHelper->getStyleProperty($element->getData($_prefix . 'padding_right'));
			$paddingBottom = $dataHelper->getStyleProperty($element->getData($_prefix . 'padding_bottom'));
			$paddingLeft   = $dataHelper->getStyleProperty($element->getData($_prefix . 'padding_left'));
			if (!$this->isNull($paddingTop) && !$this->isNull($paddingRight) && !$this->isNull($paddingBottom) && !$this->isNull($paddingLeft)) {
				if ($paddingTop == $paddingRight && $paddingTop == $paddingBottom && $paddingTop == $paddingLeft) {
					$styles['padding'] = $paddingTop . '!important';
				} else {
					$styles['padding'] = $paddingTop . ' ' . $paddingRight . ' ' . $paddingBottom . ' ' . $paddingLeft . '!important';
				}
			} else {
				$styles['padding-top']    = $dataHelper->getStyleProperty($element->getData($_prefix . 'padding_top'), true);
				$styles['padding-right']  = $dataHelper->getStyleProperty($element->getData($_prefix . 'padding_right'), true);
				$styles['padding-bottom'] = $dataHelper->getStyleProperty($element->getData($_prefix . 'padding_bottom'), true);
				$styles['padding-left']   = $dataHelper->getStyleProperty($element->getData($_prefix . 'padding_left'), true);
			}

			$marginTop    = $dataHelper->getStyleProperty($element->getData($_prefix . 'margin_top'));
			$marginRight  = $dataHelper->getStyleProperty($element->getData($_prefix . 'margin_right'));
			$marginBottom = $dataHelper->getStyleProperty($element->getData($_prefix . 'margin_bottom'));
			$marginLeft   = $dataHelper->getStyleProperty($element->getData($_prefix . 'margin_left'));
			if (!$this->isNull($marginTop) && !$this->isNull($marginRight) && !$this->isNull($marginBottom) && !$this->isNull($marginLeft)) {
				if ($marginTop == $marginRight && $marginTop == $marginBottom && $marginTop == $marginLeft) {
					$styles['margin'] = $marginTop . '!important';
				} else {
					$styles['margin'] = $marginTop . ' ' . $marginRight . ' ' . $marginBottom . ' ' . $marginLeft . '!important';
				}
			} else {
				$styles['margin-top']     = $dataHelper->getStyleProperty($element->getData($_prefix . 'margin_top'), true);
				$styles['margin-right']   = $dataHelper->getStyleProperty($element->getData($_prefix . 'margin_right'), true);
				$styles['margin-bottom']  = $dataHelper->getStyleProperty($element->getData($_prefix . 'margin_bottom'), true);
				$styles['margin-left']    = $dataHelper->getStyleProperty($element->getData($_prefix . 'margin_left'), true);
			}

			$borderStyle = $element->getData($_prefix . 'border_style');
			if ($borderStyle && $element->getData($_prefix . 'border_color')) {
				$borderTopWidth         = $dataHelper->getStyleProperty($element->getData($_prefix . 'border_top_width'));
				$borderRightWidth       = $dataHelper->getStyleProperty($element->getData($_prefix . 'border_right_width'));
				$borderBottomWidth      = $dataHelper->getStyleProperty($element->getData($_prefix . 'border_bottom_width'));
				$borderLeftWidth        = $dataHelper->getStyleProperty($element->getData($_prefix . 'border_left_width'));
				$borderColor            = $dataHelper->getStyleColor($element->getData($_prefix . 'border_color'));
				$styles['border-color'] = $dataHelper->getStyleColor($element->getData($_prefix . 'border_color'), true);

				if ($element->getData($_prefix . 'border_top_width') != '') {
					$styles['border-top-width'] = $dataHelper->getStyleProperty($element->getData($_prefix . 'border_top_width'), true);
					$styles['border-top-style'] = $borderStyle;
				}

				if ($element->getData($_prefix . 'border_right_width') != '') {
					$styles['border-right-width'] = $dataHelper->getStyleProperty($element->getData($_prefix . 'border_right_width'), true);
					$styles['border-right-style'] = $borderStyle;
				}

				if ($element->getData($_prefix . 'border_bottom_width') != '') {
					$styles['border-bottom-width'] = $dataHelper->getStyleProperty($element->getData($_prefix . 'border_bottom_width'), true);
					$styles['border-bottom-style'] = $borderStyle;
				}

				if ($element->getData($_prefix . 'border_left_width') != '') {
					$styles['border-left-width'] = $dataHelper->getStyleProperty($element->getData($_prefix . 'border_left_width'), true);
					$styles['border-left-style'] = $borderStyle;
				}

				if (isset($styles['border-top-width']) && isset($styles['border-right-width']) && isset($styles['border-bottom-style']) && isset($styles['border-left-width'])) {
					if ($borderTopWidth == $borderRightWidth && $borderTopWidth == $borderBottomWidth && $borderTopWidth == $borderLeftWidth) {
						$styles['border'] = $borderTopWidth . ' ' . $borderStyle . ' ' . $borderColor . '!important';
						unset($styles['border-top-width']);
						unset($styles['border-top-style']);
						unset($styles['border-right-width']);
						unset($styles['border-right-style']);
						unset($styles['border-bottom-width']);
						unset($styles['border-bottom-style']);
						unset($styles['border-left-width']);
						unset($styles['border-left-style']);
						unset($styles['border-color']);
					}
				}
			}

			$borderTopLeftRadius     = $dataHelper->getStyleProperty($element->getData($_prefix . 'border_top_left_radius'));
			$borderTopRightRadius    = $dataHelper->getStyleProperty($element->getData($_prefix . 'border_top_right_radius'));
			$borderBottomRightRadius = $dataHelper->getStyleProperty($element->getData($_prefix . 'border_bottom_right_radius'));
			$borderBottomLeftRadius  = $dataHelper->getStyleProperty($element->getData($_prefix . 'border_bottom_left_radius'));
			if ($borderTopLeftRadius!='' || $borderTopRightRadius!='' || $borderBottomRightRadius!='' || $borderBottomLeftRadius!='') {
				if ($borderTopLeftRadius == $borderTopRightRadius && $borderTopLeftRadius == $borderBottomRightRadius && $borderTopLeftRadius == $borderBottomLeftRadius) {
					$styles['border-radius'] = $borderTopLeftRadius . '!important';
				} else {
					if (!$borderTopLeftRadius) $borderTopLeftRadius = 0;
					if (!$borderTopRightRadius) $borderTopRightRadius = 0;
					if (!$borderBottomRightRadius) $borderBottomRightRadius = 0;
					if (!$borderBottomLeftRadius) $borderBottomLeftRadius = 0;
					$styles['border-radius'] = $borderTopLeftRadius . ' ' . $borderTopRightRadius . ' ' . $borderBottomRightRadius . ' ' . $borderBottomLeftRadius . '!important';
				}
			}

			$styles['background-color']           = $dataHelper->getStyleColor($element->getData($_prefix . 'background_color'), true);

			$_html = $dataHelper->parseStyles($styles);
			if ($_html) {
				if ($deviceType == 'custom') {
					switch ($_prefix) {
						case 'xs_':
							$html .= '@media (max-width: 575px) {';
							break;

						case 'sm_':
							$html .= '@media (max-width: 767px) {';
							break;

						case 'md_':
							$html .= '@media (max-width: 991px) {';
							break;

						case 'lg_':
							$html .= '@media (max-width: 1199px) {';
							break;

						// Defualt xl
						default:
							//$html .= '@media (min-width: 1200px) {';
							break;
					}
				}
				$html .= '.' . $element->getStyleHtmlId() . '{';
				$html .= $_html;
				$html .= '}';
				if ($deviceType == 'custom' && $_prefix) {
					$html .= '}';
				}
			}

			$backgroundImage = $element->getData($_prefix . 'background_image');
			$parallaxStyles = [];
			if ($backgroundImage) {
				$backgroundStyle = $element->getData($_prefix . 'background_style');
				$parallaxStyles['background-image'] = 'url(\'' . $dataHelper->getImageUrl($backgroundImage) . '\')';
				switch ($backgroundStyle) {
					case 'cover':
					case 'contain':
					$parallaxStyles['background-size'] = $element['background_style'];
					break;

					case 'full-width':
					$parallaxStyles['background-size'] = '100% auto';
					break;

					case 'full-height':
					$parallaxStyles['background-size'] = 'auto 100%';
					break;

					case 'repeat-x':
					$parallaxStyles['background-repeat'] = 'repeat-x';
					break;

					case 'repeat-y':
					$parallaxStyles['background-repeat'] = 'repeat-y';
					break;

					case 'no-repeat':
					case 'repeat':
					$parallaxStyles['background-repeat'] = $element['background_style'];
					break;

					default:
					$parallaxStyles['background-size'] = $backgroundStyle;
					break;
				}
				$backgroundPosition = $element->getData($_prefix . 'background_position');
				if ($backgroundPosition == 'custom') {
					$backgroundPosition = $element->getData($_prefix . 'custom_background_position');
				} else {
					$backgroundPosition = str_replace('-', ' ', $backgroundPosition);
				}
				if ($backgroundPosition) {
					$parallaxStyles['background-position'] = $backgroundPosition;
				}
			}
			if ($parallaxStyles) {
				$_html = $dataHelper->parseStyles($parallaxStyles);
				if ($_html) {
					if ($deviceType == 'custom') {
						switch ($_prefix) {
							case 'xs_':
							$html .= '@media (max-width: 575px) {';
							break;

							case 'sm_':
							$html .= '@media (max-width: 767px) {';
							break;

							case 'md_':
							$html .= '@media (max-width: 991px) {';
							break;

							case 'lg_':
							$html .= '@media (max-width: 1199px) {';
							break;

							// Defualt xl
							default:
							//$html .= '@media (min-width: 1200px) {';
							break;
						}
					}
					$html .= '.' . $element->getParallaxId() . ' .mgz-parallax-inner {';
					$html .= $_html;
					$html .= '}';
					if ($deviceType == 'custom' && $_prefix) {
						$html .= '}';
					}
				}
			}
		}
		$html .= $this->getAdditionalStyleHtml();
		if ($html) $html = '<style class="mgz-style">' . $html . '</style>';
		return $html;
	}

	/**
	 * @param  string|array $target 
	 * @param  array $styles 
	 * @param  string $suffix 
	 * @return string         
	 */
	public function getStyles($target, $styles, $suffix = '')
	{
		$htmlId = $this->getHtmlId();
		$html   = '';
		if (is_array($target)) {
			foreach ($target as $k => $_selector) {
				if (!$_selector) {
					unset($target[$k]);
				}
			}
			$i = 0;
			$count = count($target);
			foreach ($target as $_selector) {
				$html .= $htmlId . ' ' . $_selector . $suffix;
				if ($i!=$count-1)  {
					$html .= ',';
				}
				$i++;
			}
		} else {
			$html = $htmlId . ' ' . $target . $suffix;
		}
		$stylesHtml = $this->parseStyles($styles);
		if (!$stylesHtml) return;
		if ($styles) {
			$html .= '{';
			$html .= $stylesHtml;
			$html .= '}';
		}
		return $html;
	}

	/**
	 * @return string
	 */
	public function getHtmlId()
	{
		return '.mgz-element.' . $this->getElement()->getHtmlId();
	}

	/**
	 * @return string
	 */
	public function getOwlCarouselStyles()
	{
		$dataHelper = $this->getDataHelper();
		$html = '';
		$element = $this->getElement();

		// NORMAL STYLES
		$styles = [];
		$styles['color'] = $dataHelper->getStyleColor($element->getData('owl_color'));
		$styles['background-color'] = $dataHelper->getStyleColor($element->getData('owl_background_color'));
		$html .= $this->getStyles([
			'.owl-prev',
			'.owl-next',
			'.owl-dots .owl-dot:not(.active) span'
		], $styles);

		// HOVER STYLES
		$styles = [];
		$styles['color'] = $dataHelper->getStyleColor($element->getData('owl_hover_color'));
		$styles['background-color'] = $dataHelper->getStyleColor($element->getData('owl_hover_background_color'));
		$html .= $this->getStyles([
			'.owl-prev',
			'.owl-next',
			'.owl-dots .owl-dot:not(.active) span'
		], $styles, ':hover');

		$styles = [];
		$styles['color'] = $dataHelper->getStyleColor($element->getData('owl_hover_color'));
		$styles['background-color'] = $dataHelper->getStyleColor($element->getData('owl_hover_background_color'));
		$html .= $this->getStyles([
			'.owl-dots .owl-dot:not(.active)'
		], $styles, ':hover > span');

		// ACTIVE STYLES
		$styles = [];
		$styles['color'] = $dataHelper->getStyleColor($element->getData('owl_active_color'));
		$styles['background-color'] = $dataHelper->getStyleColor($element->getData('owl_active_background_color'));
		$html .= $this->getStyles([
			'.owl-dots .owl-dot.active span',
			'.mgz-carousel .owl-dots .owl-dot.active span'
		], $styles);

		$styles = [];
		$styles['background-color'] = $dataHelper->getStyleColor($element->getData('product_background'));
		$styles['padding'] = $dataHelper->getStyleProperty($element->getData('product_padding'));
		$html .= $this->getStyles('.product-item', $styles);

		return $html;
	}

	/**
	 * @return string
	 */
	public function getLineStyles()
	{
		$dataHelper = $this->getDataHelper();
		$html       = '';
		$element    = $this->getElement();

		if ($element->getData('show_line')) {
			$htmlId = '.' . $element->getHtmlId();
			$styles                     = [];
			$styles['height']           = $dataHelper->getStyleProperty($element->getData('line_width'));
			$styles['background-color'] = $dataHelper->getStyleColor($element->getData('line_color'));
			$stylesHtml = $this->parseStyles($styles);
			if ($stylesHtml) {
				$html .= $htmlId . ' .mgz-block-heading-line:before{';
				$html .= $stylesHtml;
				$html .= '}';
			}
		}

		if ($element->getData('title')) {
			$styles = [];
			$styles['color'] = $dataHelper->getStyleColor($element->getData('title_color'));
			$html .= $this->getStyles('.title', $styles);
		}

		return $html;
	}

    public function getFixedStyleProperty($type)
    {
    	$result = '';
    	switch ($type) {
    		case 'flex':
    			$result = 'display: -webkit-box;display: -webkit-flex;display: -ms-flexbox;display: flex;';
    			break;
    		
    		default:
    			# code...
    			break;
    	}
    	return $result;
    }

    /**
     * @param  string  $value       
     * @param  boolean $isImportant 
     * @return string               
     */
	public function getStyleProperty($value, $isImportant = false)
	{
		return $this->getDataHelper()->getStyleProperty($value, $isImportant);
	}

	/**
	 * @param  string  $value       
	 * @param  boolean $isImportant 
	 * @return string               
	 */
	public function getStyleColor($value, $isImportant = false)
	{
		return $this->getDataHelper()->getStyleColor($value, $isImportant);
	}

	/**
	 * @param  array $styles 
	 * @return string       
	 */
	public function parseStyles($styles)
	{
		return $this->getDataHelper()->parseStyles($styles);
	}

	/**
	 * @return string
	 */
	public function getButtonStyleHtml()
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

	public function getTabsStyleHtml()
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
		$styleHtml .= $this->getStyles($contentSelector . ':not(.mgz-active)', $styles);

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
					if (!$element->getData('spacing')) {
						$styles['margin-top'] = '-' . $tabBorderWidth;
					}
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
		$styleHtml .= $this->getStyles([
			$titleSelector . ' > a',
			$titleSelector2 . ' > a'
		], $styles);
		
		$styles['color']        = $this->getStyleColor($element->getData('tab_color'));
		$styles['background']   = $this->getStyleColor($element->getData('tab_background_color'));
		$styles['border-color'] = $this->getStyleColor($element->getData('tab_border_color'));
		$styleHtml .= $this->getStyles([
			$titleSelector . ':not(.mgz-active) > a',
			$titleSelector2 . ':not(.mgz-active) > a'
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

		$styleHtml .= $this->getLineStyles();

		return $styleHtml;
	}
}