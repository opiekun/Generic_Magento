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

namespace Magezon\Builder\Model\Rule\Condition;

use Magento\Framework\App\ObjectManager;

class Product extends \Magento\CatalogWidget\Model\Rule\Condition\Product
{
    /**
     * @inheritdoc
     */
    public function loadAttributeOptions()
    {
    	if ($this->getRequest()->getParam('mgz_builder')) {

	    	$productAttributes = $this->_productResource->loadAllAttributes()->getAttributesByCode();

	        $attributes = [];
	        foreach ($productAttributes as $attribute) {
	            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
	        }

	        $this->_addSpecialAttributes($attributes);

	        asort($attributes);
	        $this->setAttributeOption($attributes);

        	return $this;
	    }

    	return parent::loadAttributeOptions();
    }

    /**
     * @return \Magento\Framework\App\RequestInterface
     */
    public function getRequest()
    {
    	return ObjectManager::getInstance()->get('\Magento\Framework\App\RequestInterface');
    }
}