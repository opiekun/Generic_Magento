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
 * @package   Magezon_Core
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Core\Helper;

class Style extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
	 * @param \Magezon\Core\Helper\Data $dataHelper
	 */
	public function __construct(
		\Magezon\Core\Helper\Data $dataHelper
	) {
		$this->dataHelper = $dataHelper;
	}

    /**
     * @param  string|array $target 
     * @param  array $styles 
     * @param  string $suffix 
     * @return string         
     */
    public function getHtml($target, $styles, $suffix = '')
    {
        $html = '';
        if (is_array($target)) {
            foreach ($target as $k => $_selector) {
                if (!$_selector) {
                    unset($target[$k]);
                }
            }
            $i = 0;
            $count = count($target);
            foreach ($target as $_selector) {
                $html .= $_selector . $suffix;
                if ($i!=$count-1)  {
                    $html .= ',';
                }
                $i++;
            }
        } else {
            $html = $target . $suffix;
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
     * @param  array $styles 
     * @return string       
     */
    public function parseStyles($styles)
    {
        $result = '';
        foreach ($styles as $k => $v) {
            if ($v=='') continue;
            $result .= $k . ':' . $v . ';';
        }
        return $result;
    }

    /**
     * @param  string $value 
     * @return string
     */
    public function getColor($value, $important = false)
    {
        if ($value && (!$this->dataHelper->startsWith($value, '#') && !$this->dataHelper->startsWith($value, 'rgb'))) {
            if ($value != 'transparent') {
                $value = '#' . $value;
            }
        }
        if ($value && $important) {
            $value .= ' !important';
        }
        return $value;
    }

    /**
     * @param  string $value
     * @return string
     */
    public function getProperty($value, $important = false, $unit = '')
    {
    	if (!$unit) $unit = 'px';
        if (is_numeric($value)) {
            $value .= $unit;
        } else if  ($value == '-') {
        	$value = '';
        }
        if ($value && $important) {
            $value .= ' !important';
        }
        return $value;
    }

    /**
     * @param  string  $value
     * @return boolean       
     */
	public function isNull($value) {
		if (is_numeric($value)) return false;
		if ($value === '' || $value === null) {
			return true;
		}
		return false;
	}

	/**
	 * @param  array $data 
	 * @return array       
	 */
	public function getStyles($data)
	{
		$styles = [];
		$config = new \Magento\Framework\DataObject($data);

		$paddingTop    = $this->getProperty($config['padding_top']);
		$paddingRight  = $this->getProperty($config['padding_right']);
		$paddingBottom = $this->getProperty($config['padding_bottom']);
		$paddingLeft   = $this->getProperty($config['padding_left']);
		if (!$this->isNull($paddingTop) && !$this->isNull($paddingRight) && !$this->isNull($paddingBottom) && !$this->isNull($paddingLeft)) {
			if ($paddingTop == $paddingRight && $paddingTop == $paddingBottom && $paddingTop == $paddingLeft) {
				$styles['padding'] = $paddingTop;
			} else {
				$styles['padding'] = $paddingTop . ' ' . $paddingRight . ' ' . $paddingBottom . ' ' . $paddingLeft;
			}
		} else {
			$styles['padding-top']    = $paddingTop;
			$styles['padding-right']  = $paddingRight;
			$styles['padding-bottom'] = $paddingBottom;
			$styles['padding-left']   = $paddingLeft;
		}

		$marginTop    = $this->getProperty($config['margin_top']);
		$marginRight  = $this->getProperty($config['margin_right']);
		$marginBottom = $this->getProperty($config['margin_bottom']);
		$marginLeft   = $this->getProperty($config['margin_left']);
		if (!$this->isNull($marginTop) && !$this->isNull($marginRight) && !$this->isNull($marginBottom) && !$this->isNull($marginLeft)) {
			if ($marginTop == $marginRight && $marginTop == $marginBottom && $marginTop == $marginLeft) {
				$styles['margin'] = $marginTop;
			} else {
				$styles['margin'] = $marginTop . ' ' . $marginRight . ' ' . $marginBottom . ' ' . $marginLeft;
			}
		} else {
			$styles['margin-top']    = $marginTop;
			$styles['margin-right']  = $marginRight;
			$styles['margin-bottom'] = $marginBottom;
			$styles['margin-left']   = $marginLeft;
		}

		$borderStyle = $config['border_style'];
		if ($borderStyle && $config['border_color']) {
			$borderTopWidth         = $this->getProperty($config['border_top_width']);
			$borderRightWidth       = $this->getProperty($config['border_right_width']);
			$borderBottomWidth      = $this->getProperty($config['border_bottom_width']);
			$borderLeftWidth        = $this->getProperty($config['border_left_width']);
			$borderColor            = $this->getColor($config['border_color']);
			$styles['border-color'] = $this->getColor($config['border_color']);

			if (!$this->isNull($borderTopWidth)) {
				$styles['border-top-width'] = $borderTopWidth;
				$styles['border-top-style'] = $borderStyle;
			}

			if (!$this->isNull($borderRightWidth)) {
				$styles['border-right-width'] = $borderRightWidth;
				$styles['border-right-style'] = $borderStyle;
			}

			if (!$this->isNull($borderBottomWidth)) {
				$styles['border-bottom-width'] = $borderBottomWidth;
				$styles['border-bottom-style'] = $borderStyle;
			}

			if (!$this->isNull($borderLeftWidth)) {
				$styles['border-left-width'] = $borderLeftWidth;
				$styles['border-left-style'] = $borderStyle;
			}

			if (!$this->isNull($borderTopWidth) && !$this->isNull($borderRightWidth) && !$this->isNull($borderBottomWidth) && !$this->isNull($borderLeftWidth)) {
				unset($styles['border-top-style']);
				unset($styles['border-right-style']);
				unset($styles['border-bottom-style']);
				unset($styles['border-left-style']);
				$styles['border-style'] = $borderStyle;

				if ($borderTopWidth == $borderRightWidth && $borderTopWidth == $borderBottomWidth && $borderTopWidth == $borderLeftWidth) {
					$styles['border'] = $borderTopWidth . ' ' . $borderStyle . ' ' . $borderColor;
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

			if ($this->isNull($borderTopWidth) && $this->isNull($borderRightWidth) && $this->isNull($borderBottomWidth) && $this->isNull($borderLeftWidth)) {
				unset($styles['border-style']);
				unset($styles['border-color']);
			}
		}

		$borderTopLeftRadius     = $this->getProperty($config['border_top_left_radius']);
		$borderTopRightRadius    = $this->getProperty($config['border_top_right_radius']);
		$borderBottomRightRadius = $this->getProperty($config['border_bottom_right_radius']);
		$borderBottomLeftRadius  = $this->getProperty($config['border_bottom_left_radius']);
		if (!$this->isNull($borderTopLeftRadius) || !$this->isNull($borderTopRightRadius) || !$this->isNull($borderBottomRightRadius) || !$this->isNull($borderBottomLeftRadius)) {
			if ($borderTopLeftRadius == $borderTopRightRadius && $borderTopLeftRadius == $borderBottomRightRadius && $borderTopLeftRadius == $borderBottomLeftRadius) {
				$styles['border-radius'] = $borderTopLeftRadius;
			} else {
				if (!$borderTopLeftRadius) $borderTopLeftRadius = 0;
				if (!$borderTopRightRadius) $borderTopRightRadius = 0;
				if (!$borderBottomRightRadius) $borderBottomRightRadius = 0;
				if (!$borderBottomLeftRadius) $borderBottomLeftRadius = 0;
				$styles['border-radius'] = $borderTopLeftRadius . ' ' . $borderTopRightRadius . ' ' . $borderBottomRightRadius . ' ' . $borderBottomLeftRadius;
			}
		}

		$styles['background-color'] = $this->getColor($config['background_color']);
		if ($backgroundImage = $config['background_image']) {
			$styles['background-image'] = 'url(\'' . $this->dataHelper->getImageUrl($backgroundImage) . '\')';

			$backgroundPosition = $config['background_position'];
			if ($backgroundPosition == 'custom') {
				if ($config['custom_background_position']) {
					$styles['background-position'] = $config['custom_background_position'];
				}
			} else {
				$styles['background-position'] = $backgroundPosition;
			}

			$backgroundSize = $config['background_size'];
			if ($backgroundSize == 'custom') {
				if ($config['custom_background_size']) {
					$styles['background-size'] = $config['custom_background_size'];
				}
			} else {
				$styles['background-size'] = $backgroundSize;
			}

			$styles['background-repeat'] = $config['background_repeat'];
		}

		if ($config['boxshadow']) {
			$boxshadowHorizontal = $this->getProperty($config['boxshadow_horizontal']);
			$boxshadowVertical   = $this->getProperty($config['boxshadow_vertical']);
			$boxshadowBlur       = $this->getProperty($config['boxshadow_blur']);
			$boxshadowSpread     = $this->getProperty($config['boxshadow_spread']);
			$boxshadowColor      = $this->getColor($config['boxshadow_color']);
			$styles['box-shadow'] = $boxshadowHorizontal . ' ' . $boxshadowVertical . ' ' . $boxshadowBlur . ' ' . $boxshadowSpread . ' ' . $boxshadowColor;
			if ($config['boxshadow_position'] == 'inset') {
				$styles['box-shadow'] .= ' inset';
			}
		}

		return $styles;
	}
}