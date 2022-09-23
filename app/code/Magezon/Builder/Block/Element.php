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

class Element extends ElementStyle implements ElementInterface
{
	/**
	 * @var \Magezon\Builder\Data\Elements
	 */
	protected $_elementsManager;

	/**
	 * @var array
	 */
	protected $cacheElements;

	public function toHtml()
	{
		$element = $this->getElement();
		if ($element && !$this->isEnabled()) return false;
		return parent::toHtml();
	}

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
		$html = parent::_toHtml();
		$element = $this->getElement();
		if (!$this->getCoreRegistry()->registry('magezon_builder_loadelement')) {
			// Exlude Profile
			if ($element) $html = $this->getElementHtml($html);
		}
        return $html;
    }

    /**
     * @param  string $html 
     * @return string            
     */
    public function getElementHtml($html)
    {
    	$elAttributes    = $this->parseAttributes($this->getWrapperAttributes());
		$innerAttributes = $this->parseAttributes($this->getInnerAttributes());
		$result = '<div ' . $elAttributes . '>';
			$result .= '<div ' . $innerAttributes . '>';
				$result .= $this->getParallaxHtml();
				$result .= $html;
				$result .= $this->getStylesHtml();
			$result .= '</div>';
		$result .= '</div>';
		return $result;
    }

    /**
     * @return string            
     */
    public function getParallaxHtml() {
    	$html = '';
    	if ($this->isEnabledParallax()) {
			$parallaxAttributes = $this->parseAttributes($this->getParallaxAttributes());
			$html .= '<div ' . $parallaxAttributes . '>';
				$html .= '<div class="mgz-parallax-inner"></div>';
			$html .= '</div>';
		}
		return $html;
    }

    /**
     * Get design area
     *
     * @return string
     */
    public function getArea()
    {
        $area = parent::getArea();
        return 'frontend';
        if ($area == 'graphql' || $area == 'webapi_rest') $area = 'frontend';
        return $area;
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
		$httpContext   = ObjectManager::getInstance()->get(\Magento\Framework\App\Http\Context::class);
		$priceCurrency = ObjectManager::getInstance()->get(\Magento\Framework\Pricing\PriceCurrencyInterface::class);
        return [
			'MAGEZONBUILDERS_ELEMENT',
			$priceCurrency->getCurrencySymbol(),
			$this->_storeManager->getStore()->getId(),
			$this->_design->getDesignTheme()->getId(),
			$httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
			$this->getElementType(),
			$this->getElementId(),
			$this->getTemplate()
        ];
    }

    /**
     * @return \Magezon\Builder\Data\Elements
     */
    public function getElementsManager()
    {
    	if ($this->_elementsManager==NULL) {
    		$this->_elementsManager = ObjectManager::getInstance()->get(\Magezon\Builder\Data\Elements::class);
	    }
	    return $this->_elementsManager;
    }

    /**
     * @return Magezon\Builder\Data\Element
     */
    public function getBuilderElement()
    {
    	return $this->getElementsManager()->getElement($this->getElement()->getType());
    }

	/**
	 * Check whether element is enabled or not 
	 * 
	 * @return boolean
	 */
    public function isEnabled()
    {
		$enable         = true;
		$element        = $this->getElement();
		$builderElement = $this->getBuilderElement();
		if ($builderElement) {
	        if (isset($builderElement['requiredFields']) && is_array($builderElement['requiredFields'])) {
	            foreach ($builderElement['requiredFields'] as $field => $status) {
	                if ($status && !$element->getData($field)) {
	                    $enable = false;
	                    break;
	                }
	            }
	        }
	    } else {
	    	$enable = false;
	    }
        return $enable;
    }

    /**
     * Get wrapper classess
     * 
     * @return array
     */
	public function getWrapperClasses()
	{
		$element = $this->getElement();
		$builderElement = $this->getBuilderElement();
		$classes[] = $element->getHtmlId();
		$classes[] = 'mgz-element';
		if (!$builderElement->getData('is_collection')) $classes[] = 'mgz-child';
		$classes[] = 'mgz-element-' . $element->getType();
		if ($element->getAnimationIn()) {
			$classes[] = 'mgz-animated ' . $element->getAnimationIn();
			if ($element->getAnimationInfinite()) $classes[] = 'mgz-animated-infinite';
		}
		if ($element->getElClass()) $classes[] = $element->getElClass();
		if ($builderElement->getData('resizable')) {
			$xlSize = $element->getXlSize();
			$lgSize = $element->getLgSize();
			$mdSize = $element->getMdSize();
			$smSize = $element->getSmSize();
			$xsSize = $element->getXsSize();
			if (!$xlSize && !$lgSize && !$mdSize && !$smSize && !$xsSize) $xsSize = '12';
			if ($xlSize) $classes[] = 'mgz-col-xl-' . $xlSize;
			if ($lgSize) $classes[] = 'mgz-col-lg-' . $lgSize;
			if ($mdSize) $classes[] = 'mgz-col-md-' . $mdSize;
			if ($smSize) $classes[] = 'mgz-col-sm-' . $smSize;
			if ($xsSize) $classes[] = 'mgz-col-xs-' . $xsSize;
			if ($element->getXlOffsetSize()) $classes[] = 'mgz-col-xl-offset-' . $element->getXlOffsetSize();
			if ($element->getLgOffsetSize()) $classes[] = 'mgz-col-lg-offset-' . $element->getLgOffsetSize();
			if ($element->getMdOffsetSize()) $classes[] = 'mgz-col-md-offset-' . $element->getMdOffsetSize();
			if ($element->getSmOffsetSize()) $classes[] = 'mgz-col-sm-offset-' . $element->getSmOffsetSize();
			if ($element->getXsOffsetSize()) $classes[] = 'mgz-col-xs-offset-' . $element->getXsOffsetSize();
		}
		if ($element->getXlHide()) $classes[] = 'mgz-hidden-xl';
		if ($element->getLgHide()) $classes[] = 'mgz-hidden-lg';
		if ($element->getMdHide()) $classes[] = 'mgz-hidden-md';
		if ($element->getSmHide()) $classes[] = 'mgz-hidden-sm';
		if ($element->getXsHide()) $classes[] = 'mgz-hidden-xs';
		if ($element->getData('hidden_default')) $classes[] = 'mgz-hidden';
		if ($titleAlign = $element->getData('title_align')) $classes[] = 'mgz-element-title-align-' . $titleAlign;
		return $classes;
	}

    /**
     * Get inner classess
     * 
     * @return array
     */
	public function getInnerClasses()
	{
		$classes = [];
		$element = $this->getElement();
		$classes[] = 'mgz-element-inner';
		$classes[] = $element->getStyleHtmlId();
		if ($element->getElInnerClass()) $classes[] = $element->getElInnerClass();
		return $classes;
	}

	/**
	 * Get elem attributes
	 * 
	 * @return array
	 */
	public function getWrapperAttributes()
	{
		$element = $this->getElement();
		$attrs = [];
		if ($element->getElId()) $attrs['id'] = $element->getElId();
		if ($classes = $this->getWrapperClasses()) $attrs['class'] = implode(' ', $classes);
		if ($element->getData('animation_in')) {
			if ($element->hasData('animation_duration')) {
	    		$attrs['data-animation-duration'] = $element->getData('animation_duration');
	    	}
			if ($element->hasData('animation_delay')) {
	    		$attrs['data-animation-delay'] = $element->getData('animation_delay');
	    	}
	    }

		return $attrs;
	}

	/**
	 * Get elem inner attributes
	 * 
	 * @return array
	 */
	public function getInnerAttributes()
	{
		$element = $this->getElement();
		$classes = $this->getInnerClasses();
		if ($classes) $attrs['class'] = implode(' ', $classes);
		return $attrs;
	}

	/**
	 * Check whether parallax is enabled or not 
	 * 
	 * @return boolean
	 */
	public function isEnabledParallax()
	{
		$element = $this->getElement();
		$backgroundColor = $element->getBackgroundColor();
		$backgroundImage = $element->getBackgroundImage();
		$backgroundVideo = $element->getBackgroundVideo();
		$backgroundType  = $element->getBackgroundType();
		return ($backgroundColor || $backgroundImage || ($backgroundVideo && $backgroundType == 'yt_vm_video'));
	}

	/**
	 * Get parallax attributes
	 * 
	 * @return array
	 */
	public function getParallaxAttributes()
	{
		$element         = $this->getElement();
		$type            = $element->getParallaxType();
		$backgroundVideo = $element->getBackgroundVideo();
		$backgroundType  = $element->getBackgroundType();

		$classes = $attrs = [];
		$classes[] = 'mgz-parallax';
		$classes[] = $element->getParallaxId();

		$attrs['data-background-type'] = $backgroundType;
		$attrs['data-parallax-type'] = $type;
		

		$backgroundPosition = str_replace('-', ' ', $element->getBackgroundPosition());
		if ($backgroundPosition) $attrs['data-parallax-image-background-position'] = $backgroundPosition;

		if ($backgroundType == 'yt_vm_video') {
			$attrs['data-parallax-video']            = $backgroundVideo;
			$attrs['data-parallax-video-start-time'] = $element->getVideoStartTime();
			$attrs['data-parallax-video-end-time']   = $element->getVideoEndTime();
			if ($element->getVideoVolume()) $attrs['data-parallax-video-volume'] = $element->getVideoVolume();
			if ($element->getParallaxVideoAlwaysPlay()) $attrs['data-parallax-video-always-play'] = 'true';
		}

		if ($backgroundType == 'yt_vm_video') $attrs['data-parallax-video-mobile'] = $element->getVideoMobile();

		if ($type == 'scroll' || $type == 'scale' || $type == 'opacity' || $type == 'scroll-opacity' || $type == 'scale-opacity') {
			$attrs['data-parallax-type'] = $type;
			$attrs['data-parallax-speed'] = $element->getParallaxSpeed();
			$attrs['data-parallax-mobile'] = $element->getParallaxMobile();
		}

		if ($element->getMouseParallax()) {
			$attrs['data-parallax-mouse-parallax-size'] = $element->getMouseParallaxSize();
			$attrs['data-parallax-mouse-parallax-speed'] = $element->getMouseParallaxSpeed();
			$classes[] = 'mgz-parallax-mouse-parallax';
		}

		if ($classes) $attrs['class'] = implode(' ', $classes);

		return $attrs;
	}

	/**
	 * @return array
	 */
	public function getElements()
	{
		if ($this->cacheElements == NULL) {
			if ($this->getElement()) {
				$elements = $this->getElement()->getElements();	
			} else {
				$elements = $this->getData('elements');
			}
			if (!$elements) $elements = [];
			$this->cacheElements = $this->processElements($elements);
		}
		return $this->cacheElements;
	}

	/**
	 * @param  array  $elements 
	 * @param  boolean $nested   
	 * @return array            
	 */
	public function processElements($elements, $nested = false)
	{
		$newElements = [];
		foreach ($elements as $data) {
			if (!isset($data['type']) || !$data['type'] || !isset($data['id']) || !$data['id']
				|| (isset($data['disable_element']) && $data['disable_element'])
			) {
				continue;
			}

			$builderElement = $this->getElementsManager()->getElement($data['type']);

			if (!$builderElement) continue;

			$element = $this->getElementsManager()->getElementModel($data);
			$element->getElementBlock()->setGlobalData($this->getData('global_data'));

			// Leak Memory - process all sub childrens
			if (isset($data['elements']) && is_array($data['elements']) && $nested) {
				$element->setElements($this->processElements($data['elements'], $nested));
			}
			$element->setParentElement($this->getElement());

			$newElements[] = $element;
		}
		return $newElements;
	}

    /**
     * @return \Magezon\Builder\Helper\Data
     */
    public function getDataHelper() {
    	return ObjectManager::getInstance()->get('\Magezon\Builder\Helper\Data');
    }

    /**
     * @return \Magezon\Core\Helper\Data
     */
    public function getCoreHelper() {
    	return ObjectManager::getInstance()->get('\Magezon\Core\Helper\Data');
    }

    /**
     * @return \Magento\Framework\Registry
     */
    public function getCoreRegistry() {
    	return ObjectManager::getInstance()->get('\Magento\Framework\Registry');
    }

	/**
	 * @param  array $array 
	 * @return string       
	 */
	public function parseJson($array)
	{
		$result = '';
		if (is_array($array)) {
			foreach ($array as $k => $v) {
				if (!$v) continue;
				if ($v === true) $v = 'true';
				if ($v === false) $v = 'false';
				if ($result) $result .= ',';
				$result .= '\'' . $k . '\': ' . $v;
			}
		}
		return $result;
	}

	/**
	 * @param  array $attributes 
	 * @return string       
	 */
	public function parseAttributes($attributes)
	{
		if (!is_array($attributes)) return;
		$result = '';
		$count  = count($attributes);
		$index  = 0;
		foreach ($attributes as $k => $v) {
			if (!$v) continue;
			if ($index!=0 && $index!=$count) {
				$result .= ' ';
			}
			$result .= $k . '="' . $v . '"';
			$index++;
		}
		return $result;
	}

	/**
	 * @param  string $name
	 * @return string      
	 */
    public function renderElement($name)
    {
    	try {
    		return $this->getLayout()->renderElement($name);
    	} catch (\Exception $e) {

    	}
    }

    /**
     * @param  array $items 
     * @return array        
     */
    public function toObjectArray($items)
    {
    	$result = [];
    	if (is_array($items)) {
	    	foreach ($items as $item) {
				$result[] = new \Magento\Framework\DataObject($item);
			}
		}
    	return $result;
    }

    /**
     * @param string $key
     */
    public function addGlobalData($key, $data)
    {
    	$globalData = $this->getData('global_data') ? $this->getData('global_data') : [];
    	$globalData[$key] = $data;
    	$this->setData('global_data', $globalData);
    	return $this;
    }

    /**
     * @param  string $key
     */
    public function getGlobalData($key)
    {
    	$globalData = $this->getData('global_data') ? $this->getData('global_data') : [];
    	if (isset($globalData[$key])) return $globalData[$key];
    }

    /**
     * @return array
     */
    public function getOwlCarouselOptions()
    {
    	$element = $this->getElement();
    	if ($element->getData('owl_item_xl')) $options['item_xl'] = $element->getData('owl_item_xl');
    	if ($element->getData('owl_item_lg')) $options['item_lg'] = $element->getData('owl_item_lg');
    	if ($element->getData('owl_item_md')) $options['item_md'] = $element->getData('owl_item_md');
    	if ($element->getData('owl_item_sm')) $options['item_sm'] = $element->getData('owl_item_sm');
    	if ($element->getData('owl_item_xs')) $options['item_xs'] = $element->getData('owl_item_xs');
		$lazyLoad                      = $element->getData('owl_lazyload');
		$options['nav']                = $element->getData('owl_nav') ? true : false;
		$options['dots']               = $element->getData('owl_dots') ? true : false;
		$options['autoplayHoverPause'] = $element->getData('owl_autoplay_hover_pause') ? true : false;
		$options['autoplay']           = $element->getData('owl_autoplay') ? true : false;
		$options['autoplayTimeout']    = $element->getData('owl_autoplay_timeout');
		$options['lazyLoad']           = $lazyLoad ? true : false;
		$options['loop']               = $element->getData('owl_loop') ? true : false;
		$options['margin']             = (int) $element->getData('owl_margin');
		$options['autoHeight']         = $element->getData('owl_auto_height') ? true : false;
		$options['rtl']                = $element->getData('owl_rtl') ? true : false;
		$options['center']             = $element->getData('owl_center') ? true : false;
		$options['slideBy']            = $element->getData('owl_slide_by') ? $element->getData('owl_slide_by') : 1;
		$options['animateIn']          = $element->getData('owl_animate_in') ? $element->getData('owl_animate_in') : '';
		$options['animateOut']         = $element->getData('owl_animate_out') ? $element->getData('owl_animate_out') : '';
		$options['stagePadding']       = $element->getData('owl_stage_padding') ? (int)$element->getData('owl_stage_padding') : 0;
		if ($element->getData('owl_active')) $options['owl_active'] = (int)$element->getData('owl_active');
		if ($element->getData('owl_dots_speed')) $options['dotsSpeed'] = $element->getData('owl_dots_speed');
		if ($element->getData('owl_autoplay_speed')) $options['autoplaySpeed'] = $element->getData('owl_autoplay_speed');
    	return $options;
    }

    /**
     * @return array
     */
    public function getOwlCarouselClasses()
    {
		$element     = $this->getElement();
		$dotsInsie   = $element->getData('owl_dots_insie');
		$navPosition = $element->getData('owl_nav_position');
		$navSize     = $element->getData('owl_nav_size');
    	if ($dotsInsie) $classes[] = 'mgz-carousel-dot-inside';
    	$classes[] = 'mgz-carousel-nav-position-' . $navPosition;
    	$classes[] = 'mgz-carousel-nav-size-' . $navSize;
    	if ($element->getData('product_equalheight')) $classes[] = 'mgz-carousel-equal-height';
    	return $classes;
    }

    /**
     * Escape a string for the HTML attribute context
     *
     * @param string $string
     * @param boolean $escapeSingleQuote
     * @return string
     */
    public function escapeHtmlAttr($string, $escapeSingleQuote = true)
    {
		if (!$string) return;
        return $this->_escaper->escapeHtmlAttr($string, $escapeSingleQuote);
    }

    /**
     * @param  array $data 
     * @return array       
     */
    public function getLinkParams($data)
    {
    	if (isset($data['url'])) {
    		$data['url'] = str_replace(['//', 'https:/', 'http:/'], ['/', 'https://', 'http://'], $data['url']);
    	}
    	$coreHelper = $this->getCoreHelper();
    	$params = [
			'type'     => 'custom',
			'url'      => '',
			'id'       => 0,
			'title'    => '',
			'extra'    => '',
			'nofollow' => 0,
			'blank'    => 0
    	];
    	if ($data) {
	    	if (is_string($data)) {
				$params['url']  = $data;
				$params['type'] = 'custom';
	    	} else {
				$params = array_merge($params, $data);
	    	}
	    }
    	if ($params['extra']) {
    		if ($coreHelper->startsWith($params['extra'], '#')) {
    			$params['url'] = $params['url'] . str_replace(['&'], ['%26'], $params['extra']);
    		} else {
    			$params['url'] = $params['url'] . '?' . str_replace(['&'], ['%26'], $params['extra']);
    		}
    	}
    	$params['url'] = $coreHelper->filter(stripslashes($params['url']));
    	foreach ($params as $key => &$value) {
    		if ($value == 'true') $params[$key] = 1;
    		if ($value == 'false') $params[$key] = 0;
    	}
    	return $params;
    }

	public function isNull($value) {
		if (is_numeric($value)) return false;
		if ($value === '' || $value === null) {
			return true;
		}
		return false;
	}

    /**
     * Get file name from source (URI) without last extension.
     *
     * @param string $source
     * @param bool $withExtension
     * @return string
     */
    public function getFilename($source, $withExtension = false)
    {
        $file = str_replace(dirname($source) . '/', '', $source);
        if (!$withExtension) {
            $file = substr($file, 0, strrpos($file, '.'));
        }
        return $file;
    }
}