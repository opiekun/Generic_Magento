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
 * @package   Magezon_NinjaMenus
 * @copyright Copyright (C) 2018 Magezon (https://www.magezon.com)
 */

namespace Magezon\NinjaMenus\Block\Adminhtml;

class Scripts extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magezon\Core\Model\Source\Categories
     */
    protected $categories;

    /**
     * @param \Magento\Backend\Block\Template\Context $context    
     * @param \Magezon\Core\Model\Source\Categories   $categories 
     * @param array                                   $data       
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magezon\Core\Model\Source\Categories $categories,
        array $data = []
    ) {
        parent::__construct($context);
        $this->categories = $categories;
    }

    /**
     * @return array
     */
    public function getStoreCategories()
    {
        return $this->categories->getCategoriesTree();
    }
}
