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

namespace Magezon\TabGrid\Block\Widget\Grid\Column\Renderer\Options;

class Extended extends \Magezon\TabGrid\Block\Widget\Grid\Column\Renderer\Options
{
    /**
     * @var \Magezon\TabGrid\Block\Widget\Grid\Column\Renderer\Options\Converter
     */
    protected $_converter;

    /**
     * @param \Magezon\TabGrid\Block\Context $context
     * @param \Magezon\TabGrid\Block\Widget\Grid\Column\Renderer\Options\Converter $converter
     * @param array $data
     */
    public function __construct(
        \Magezon\TabGrid\Block\Context $context,
        \Magezon\TabGrid\Block\Widget\Grid\Column\Renderer\Options\Converter $converter,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_converter = $converter;
    }

    /**
     * Prepare data for renderer
     *
     * @return array
     */
    public function _getOptions()
    {
        $options = $this->getColumn()->getOptions();
        return $this->_converter->toTreeArray($options);
    }
}
