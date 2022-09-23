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

namespace Magezon\NinjaMenus\Block\Product;

class Breadcrumbs extends \Magento\Catalog\Block\Product\View
{
    /**
     * @var string
     */
    protected $_template = 'Magezon_NinjaMenus::product/breadcrumbs.phtml';

    /**
     * @return array
     */
    public function getCategories()
    {
        $list     = [];
        $product  = $this->getProduct();
        $category = $product->getCategory();
        if ($category) {
            $categories = $category->getParentCategories();
            foreach ($categories as $_category) {
                $list[] = [
                    'label' => $_category->getName(),
                    'title' => $_category->getName(),
                    'link'  => $_category->getUrl()
                ];
            }
        }
        return $list;
    }
}