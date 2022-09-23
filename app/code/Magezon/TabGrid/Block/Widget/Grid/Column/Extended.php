<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://magezon.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://magento.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_TabGrid
 * @copyright Copyright (C) 2017 Magezon (https://magezon.com)
 */

namespace Magezon\TabGrid\Block\Widget\Grid\Column;

class Extended extends \Magezon\TabGrid\Block\Widget\Grid\Column
{
    /**
     * @param \Magezon\TabGrid\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(\Magezon\TabGrid\Block\Template\Context $context, array $data = [])
    {
        $this->_rendererTypes['options']  = 'Magezon\TabGrid\Block\Widget\Grid\Column\Renderer\Options\Extended';
        $this->_filterTypes['options']    = 'Magezon\TabGrid\Block\Widget\Grid\Column\Filter\Select\Extended';
        $this->_rendererTypes['select']   = 'Magezon\TabGrid\Block\Widget\Grid\Column\Renderer\Select\Extended';
        $this->_rendererTypes['checkbox'] = 'Magezon\TabGrid\Block\Widget\Grid\Column\Renderer\Checkboxes\Extended';
        $this->_rendererTypes['radio']    = 'Magezon\TabGrid\Block\Widget\Grid\Column\Renderer\Radio\Extended';

        parent::__construct($context, $data);
    }
}
