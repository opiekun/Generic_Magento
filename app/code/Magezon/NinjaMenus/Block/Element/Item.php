<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more inmenuation.
 *
 * @category  Magezon
 * @package   Magezon_NinjaMenus
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\NinjaMenus\Block\Element;

class Item extends \Magezon\Builder\Block\Element
{
	/**
	 * @var \Magezon\Core\Helper\Data
	 */
	protected $coreHelper;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context    
	 * @param \Magezon\Core\Helper\Data                        $coreHelper 
	 * @param array                                            $data       
	 */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magezon\Core\Helper\Data $coreHelper,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->coreHelper = $coreHelper;
	}

    /**
     * @param  string $html 
     * @return string            
     */
    public function getElementHtml($html)
    {
		$elAttributes = $this->parseAttributes($this->getWrapperAttributes());
		$result = '<div ' . $elAttributes . '>';
			$result .= $html;
			$result .= $this->getStylesHtml();
		$result .= '</div>';
		return $result;
    }

    /**
     * Get inner classess
     * 
     * @return array
     */
	public function getInnerClasses()
	{
		$classes = parent::getInnerClasses();
		array_unshift($classes, 'item-submenu');
		return $classes;
	}

	/**
	 * Get elem attributes
	 * 
	 * @return array
	 */
	public function getWrapperAttributes()
	{
		$attrs = parent::getWrapperAttributes();
		$element = $this->getElement();
		$animateIn = $element->getData('submenu_animate_in');
		$animateOut = $element->getData('submenu_animate_out');
		$animationDuration = $element->getData('submenu_animation_duration') != '' ? $element->getData('submenu_animation_duration') * 1000 : '';
		if ($animateIn) $attrs['data-animate-in'] = $animateIn;
		if ($animateOut) $attrs['data-animate-out'] = $animateOut;
		if ($animationDuration) $attrs['data-animation-duration'] = $animationDuration;

		$elements   = $this->getElements();
		$count      = count($elements);
		$caret      = $element->getData('caret');
		$caretHover = $element->getData('caret_hover');
		$icon       = $element->getData('icon');
		$iconHover  = $element->getData('icon_hover');
		if ($caret == 'fas mgz-fa-angle-down' && !$caretHover) $caret = '';
		if ($count && $caret) {
			if ($caret != 'fas mgz-fa-angle-down') {
				$attrs['data-caret'] = $caret;
			}
			if ($caretHover && $caretHover != 'fas mgz-fa-angle-up') $attrs['data-caret-hover'] = $caretHover;
		}
		if ($icon) {
			$attrs['data-icon'] = $icon;
			if ($iconHover) $attrs['data-icon-hover'] = $iconHover;
		}

		return $attrs;
	}

	/**
	 * @return array
	 */
	public function getWrapperClasses()
	{
		$element         = $this->getElement();
		$classes         = parent::getWrapperClasses();
		$classes[]       = 'nav-item';
		$submenuType     = $element->getData('submenu_type');
		$submenuPosition = $element->getData('submenu_position');
		$label           = $element->getData('label');
		$labelPosition   = $element->getData('label_position');
		$classes = array_diff($classes, ['mgz-element-menu_item']);

		if ($element->getData('elements') || $element->getData('parent_id')) {
			$classes[] = $submenuType;
			$classes[] = $submenuPosition;
		}

		if ($element->getData('parent_id')) {
			$classes[] = 'item-autolist';
			$classes[] = 'item-autolist-col' . $element->getData('subcategories_col');
		}

		if ($label) {
			$classes[] = 'label-' . $labelPosition;
		}

		if ($element->getData('hide_on_mobile')) {
			$classes[] = 'ninjamenus-hide-mobile';
		}

		if ($element->getData('hide_on_desktop')) {
			$classes[] = 'ninjamenus-hide-desktop';
		}

		if ($element->getData('hide_on_sticky')) {
			$classes[] = 'ninjamenus-hide-sticky';
		}

		if ($element->getData('submenu_fullwidth')) {
			$classes[] = 'nav-item-static';
		}

		$element = $this->getParentElement();
		if (!$element) $classes[] = 'level0';

		return $classes;
	}

	/**
	 * @return string
	 */
	public function getLink()
	{
		$link    = '#';
		$element = $this->getElement();
		$type    = $element->getData('item_type');

		switch ($type) {
			case 'category':
				$collection = $this->getGlobalData('category_collection');
				if ($collection) {
					$category = $collection->getItemById($element->getData('category_id'));
					if ($category) {
						$link = $category->getUrl();
						if ($element->getData('cat_name')) {
							$this->setData('title', $category->getName());
						}
					}
				}
				break;

			case 'product':
				$collection = $this->getGlobalData('product_collection');
				if ($collection) {
					$product = $collection->getItemById($element->getData('product_id'));
					if ($product) $link = $product->getProductUrl();
				}
				break;

			case 'page':
				$collection = $this->getGlobalData('page_collection');
				if ($collection) {
					$page = $collection->getItemById($element->getData('page_id'));
					if ($page) $link = $this->_urlBuilder->getUrl(null, ['_direct' => $page->getIdentifier()]);
				}
				break;

			case 'custom':
				$customLink = $element->getData('custom_link');
				if ($customLink) $link = $this->coreHelper->filter($element->getData('custom_link'));
				break;
		}

		return $link;
	}
	/**
	 * @return string
	 */
	public function getAdditionalStyleHtml()
	{
		$styleHtml = '';
		$element = $this->getElement();
		$id = $element->getData('id');
		$styles = [];
		$styles['width'] = $this->getStyleProperty($element->getData('submenu_width'), true);
		if ($animationDuration = $element->getData('submenu_animation_duration')) {
			$styles['animation-duration'] = $animationDuration . 's';
		}
		if ($_styleHtml = $this->parseStyles($styles)) {
			$styleHtml .= '.' . $id . ' > .item-submenu{' . $_styleHtml . '}';
		}

		if ($element->getData('item_inline_css')) {
			$styleHtml .= '.' . $id . ' > a{' . $element->getData('item_inline_css') . '}';
		}

		if ($element->getData('submenu_inline_css')) {
			$styleHtml .= '.' . $element->getStyleHtmlId() . '{' . $element->getData('submenu_inline_css') . '}';
		}

		$styles = [];
		if ($element->getData('item_padding')!='') {
			$styles['padding'] = $element->getData('item_padding') . ' !important';
		}
		$styles['font-size']   = $this->getStyleProperty($element->getData('item_font_size'));
		if ($element->getData('item_font_weight')) {
			$styles['font-weight'] = $element->getData('item_font_weight') . '!important';
		}		
		if ($_styleHtml = $this->parseStyles($styles)) {
			$styleHtml .= '.' . $id . '>a{' . $_styleHtml . '}';
		}

		$styles = [];
		$styles['color']      = $this->getStyleColor($element->getData('title_color'), true);
		$styles['background'] = $this->getStyleColor($element->getData('title_background_color'), true);
		if ($_styleHtml = $this->parseStyles($styles)) {
			$styleHtml .= '.' . $id . '>a{' . $_styleHtml . '}';
		}

		$styles = [];
		$styles['color']      = $this->getStyleColor($element->getData('title_hover_color'), true);
		$styles['background'] = $this->getStyleColor($element->getData('title_hover_background_color'), true);
		if ($_styleHtml = $this->parseStyles($styles)) {
			$styleHtml .= '.' . $id . ':hover>a{' . $_styleHtml . '}';
		}

		$styles = [];
		$styles['color']      = $this->getStyleColor($element->getData('title_active_color'), true);
		$styles['background'] = $this->getStyleColor($element->getData('title_active_background_color'), true);
		if ($_styleHtml = $this->parseStyles($styles)) {
			$styleHtml .= '.' . $id . '.active>a{' . $_styleHtml . '}';
		}

		if ($element->getData('label')) {
			$styles = [];
			$styles['color']      = $this->getStyleColor($element->getData('label_color'));
			$styles['background'] = $this->getStyleColor($element->getData('label_background_color'));
			if ($_styleHtml = $this->parseStyles($styles)) {
				$styleHtml .= '.' . $id . '>a .label{' . $_styleHtml . '}';
			}
			$styles = [];
			$styles['border-top-color'] = $this->getStyleColor($element->getData('label_background_color'));
			if ($_styleHtml = $this->parseStyles($styles)) {
				$styleHtml .= '.' . $id . '>a .label:before{' . $_styleHtml . '}';
			}

			$styles = [];
			$styles['color']      = $this->getStyleColor($element->getData('label_hover_color'));
			$styles['background'] = $this->getStyleColor($element->getData('label_hover_background_color'));
			if ($_styleHtml = $this->parseStyles($styles)) {
				$styleHtml .= '.' . $id . ':hover>a .label{' . $_styleHtml . '}';
			}
			$styles = [];
			$styles['border-top-color'] = $this->getStyleColor($element->getData('label_hover_background_color'));
			if ($_styleHtml = $this->parseStyles($styles)) {
				$styleHtml .= '.' . $id . ':hover>a .label:before{' . $_styleHtml . '}';
			}

			$styles = [];
			$styles['color']      = $this->getStyleColor($element->getData('label_active_color'));
			$styles['background'] = $this->getStyleColor($element->getData('label_active_background_color'));
			if ($_styleHtml = $this->parseStyles($styles)) {
				$styleHtml .= '.' . $id . '.active>a .label{' . $_styleHtml . '}';
			}
			$styles = [];
			$styles['border-top-color'] = $this->getStyleColor($element->getData('label_active_background_color'));
			if ($_styleHtml = $this->parseStyles($styles)) {
				$styleHtml .= '.' . $id . '.active>a .label:before{' . $_styleHtml . '}';
			}
		}

		$styles = [];
		$styles['padding'] = $this->getStyleProperty($element->getData('submenu_desktop_padding'));
		if ($_styleHtml = $this->parseStyles($styles)) {
			$styleHtml .= '.ninjamenus-desktop .' . $id . ' > .item-submenu {' . $_styleHtml . '}';
		}

		$styles = [];
		$styles['padding'] = $this->getStyleProperty($element->getData('submenu_mobile_padding'));
		if ($_styleHtml = $this->parseStyles($styles)) {
			$styleHtml .= '.ninjamenus-mobile .' . $id . ' > .item-submenu {' . $_styleHtml . '}';
		}

		$styles = [];
		$styles['color'] = $this->getStyleColor($element->getData('icon_color'));
		if ($_styleHtml = $this->parseStyles($styles)) {
			$styleHtml .= '.' . $id . ' >a .item-icon{' . $_styleHtml . '}';
		}

		return $styleHtml;
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		$element = $this->getElement();
		$title = $this->getData('title');
		if (!$title) {
			$title = $element->getData('title');
		}
		return $title;
	}

    /**
     * @return array
     */
    public function getElements()
    {
    	if ($this->cacheElements == NULL) {
			$elements = [];
			$element  = $this->getElement();
	        if ($parentId = $element->getData('parent_id')) {
	        	$collection = $this->getGlobalData('category_collection');
	        	if ($collection) {
		        	$category = $collection->getItemById($parentId);
					if ($category && $category->getSubCategories()) {
						$elements = $this->prepareSubCategories($category->getSubCategories());
					}
				}
				$this->cacheElements = $this->processElements($elements);
	        } else {
	        	$this->cacheElements = parent::getElements();	
	        }
	    }
	    return $this->cacheElements;
    }

    public function prepareSubCategories($categories) {
    	$_elements = [];
    	foreach ($categories as $item) {
    		$_category = $item['category'];
    		$_element = [
				'type'         => 'menu_item',
				'submenu_type' => 'stack',
				'id'           => 'item-cat' . $_category->getId(),
				'title'        => $_category->getName(),
				'item_type'    => 'custom',
				'custom_link'  => $_category->getUrl()
    		];
    		if (isset($item['children']) && $item['children']) {
				$_element['elements'] = $this->prepareSubCategories($item['children']);
    		}
    		$_elements[] = $_element;
    	}
    	return $_elements;
    }
}
