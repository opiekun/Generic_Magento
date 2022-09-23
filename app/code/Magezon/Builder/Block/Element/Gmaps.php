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

class Gmaps extends \Magezon\Builder\Block\Element
{
	/**
	 * @var array
	 */
	protected $_items;

	/**
	 * @var \Magezon\Builder\Helper\Data
	 */
	protected $builderHelper;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context       
	 * @param \Magezon\Builder\Helper\Data                     $builderHelper 
	 * @param array                                            $data          
	 */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
       	\Magezon\Builder\Helper\Data $builderHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
		$this->builderHelper = $builderHelper;
    }

	/**
	 * @return boolean
	 */
	public function isEnabled()
	{
		if (!$this->builderHelper->getGoogleMapApi()) return false;
		return parent::isEnabled();
	}

	public function getItems()
	{
		if ($this->_items === NULL) {
			$newItems = [];
			$items    = $this->getElement()->getData('items');
			if ($items) {
				foreach ($items as $item) {
					if (isset($item['image']) && $item['image']) {
						$item['image'] = $this->builderHelper->getImageUrl($item['image']);
					}
					$newItems[] = $item;
				}
			}
			$this->_items = $newItems;
		}
		return $this->_items;
	}

	public function getCenterItem()
	{
		$result = '';
		$items = $this->getItems();
		if ($items) {
			foreach ($items as $_item) {
				if (isset($_item['center']) && $_item['center'] && $_item['lat'] && $_item['lng']) {
					$result = $_item;
					break;
				}
			}
			if (!$result) {
				foreach ($items as $_item) {
					if ($_item['lat'] && $_item['lng']) {
						$result = $_item;
						break;
					}
				}
			}
		}
		return $result;
	}
	/**
	 * @return string
	 */
	public function getAdditionalStyleHtml()
	{
		$styleHtml = '';
		$element   = $this->getElement();

		$styles = [];
		$styles['width']  = $this->getStyleProperty($element->getData('map_width'));
		$styles['height'] = $this->getStyleProperty($element->getData('map_height'));
		$styleHtml .= $this->getStyles('#' . $element->getId() . '-map', $styles);

		$styles = [];
		$styles['color']      = $this->getStyleColor($element->getData('infobox_text_color'));
		$styles['background'] = $this->getStyleColor($element->getData('infobox_background_color'));
		$styles['width']      = $this->getStyleProperty($element->getData('infobox_width'));
		$styleHtml .= $this->getStyles('.gm-style-iw-c', $styles);

		$styles = [];
		$styles['background'] = $this->getStyleColor($element->getData('infobox_background_color'));
		$styleHtml .= $this->getStyles('.gm-style-iw-t::after', $styles);

		return $styleHtml;
	}
}