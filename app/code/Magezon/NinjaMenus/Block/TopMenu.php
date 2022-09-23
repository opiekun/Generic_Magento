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

namespace Magezon\NinjaMenus\Block;

class TopMenu extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magezon\NinjaMenus\Helper\Data
     */
    protected $menuHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context    
     * @param \Magezon\NinjaMenus\Helper\Menu                  $menuHelper 
     * @param array                                            $data       
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magezon\NinjaMenus\Helper\Menu $menuHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->menuHelper = $menuHelper;
    }

    public function getMenuHtml()
    {
        return $this->menuHelper->getMenuHtml($this->getIdentifier(), 'ninjamenus-top');
    }
}
