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

class Builder extends \Magezon\Builder\Block\Builder
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context  $context        
     * @param \Magezon\NinjaMenus\Model\CompositeConfigProvider $configProvider 
     * @param array                                             $data           
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magezon\NinjaMenus\Model\CompositeConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct($context, $configProvider, $data);
    }

    /**
     * @return array
     * @codeCoverageIgnore
     */
    public function getBuilderConfig()
    {   
        $config = parent::getBuilderConfig();
        $config['mainElement'] = 'menu_item';
        return $config;
    }
}