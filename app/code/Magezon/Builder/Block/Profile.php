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

class Profile extends \Magezon\Builder\Block\Element
{
	/**
	 * @var string
	 */
	protected $_template = 'Magezon_Builder::profile.phtml';

	/**
	 * @var array
	 */
	protected $_flatElements;

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

    protected function _toHtml()
    {	
    	$count = count($this->getElements());
    	if (!$count) return;
    	$html = $this->builderHelper->minify(parent::_toHtml());
    	return $html;
    }

	/**
	 * @param array $elements
	 */
	public function processFlatElements($elements)
	{
		foreach ($elements as $element) {
			if ($element->getData('elements') && $element->getElements()) {
				$this->processFlatElements($element->getElements());
			}
			$this->_flatElements[] = $element;
		}
	}

	/**
	 * @return array
	 */
	public function getFlatElements()
	{
		if ($this->_flatElements === null) {
			if ($this->getElement()) {
				$elements = $this->getElement()->getElements();	
			} else {
				$elements = $this->getData('elements');
			}
			if (!$elements || !is_array($elements)) $elements = [];
			$newElements = $this->processElements($elements, true);
			$this->processFlatElements($newElements, true);
			if ($this->_flatElements === null) {
				$this->_flatElements = [];
			}
		}
		return $this->_flatElements;
	}

	/**
	 * @return string
	 */
	public function getStylesHtml()
	{
		$html = '';
		$flatElements = $this->getFlatElements();
		if ($flatElements) {
			foreach ($flatElements as $element) {
				$styleHtml = $element->getElementBlock()->getStylesHtml();
				$styleHtml = str_replace('<style ', '<style id="' . $element->getId() . '-style" ', $styleHtml);
				$html .= $styleHtml;
			}
		}
		return $html;
	}
}