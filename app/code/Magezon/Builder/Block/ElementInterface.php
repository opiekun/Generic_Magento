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

interface ElementInterface
{
	/**
	 * Check whether element is enabled or not 
	 * 
	 * @return boolean
	 */
	public function isEnabled();

    /**
     * Get wrapper classess
     * 
     * @return array
     */
	public function getWrapperClasses();

    /**
     * Get inner classess
     * 
     * @return array
     */
	public function getInnerClasses();

	/**
	 * Get styles html of element
	 * 
	 * @return string
	 */
	public function getStylesHtml();

	/**
	 * Get elem attributes
	 * 
	 * @return array
	 */
	public function getWrapperAttributes();

	/**
	 * Get elem inner attributes
	 * 
	 * @return array
	 */
	public function getInnerAttributes();

	/**
	 * Check whether parallax is enabled or not 
	 * 
	 * @return boolean
	 */
	public function isEnabledParallax();

	/**
	 * Get parallax attributes
	 * 
	 * @return array
	 */
	public function getParallaxAttributes();
}
