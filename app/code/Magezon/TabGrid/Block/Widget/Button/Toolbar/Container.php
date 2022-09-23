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

namespace Magezon\TabGrid\Block\Widget\Button\Toolbar;

use Magezon\TabGrid\Block\Widget\Button\ContextInterface;

class Container extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * Create button renderer
     *
     * @param string $blockName
     * @param string $blockClassName
     * @return \Magezon\TabGrid\Block\Widget\Button
     */
    protected function createButton($blockName, $blockClassName = null)
    {
        if (null === $blockClassName) {
            $blockClassName = 'Magezon\TabGrid\Block\Widget\Button';
        }
        return $this->getLayout()->createBlock($blockClassName, $blockName);
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        $item    = $this->getButtonItem();
        $context = $this->getContext();

        if ($item && $context && $context->canRender($item)) {
            $data           = $item->getData();
            $blockClassName = isset($data['class_name']) ? $data['class_name'] : null;
            $buttonName     = $this->getContext()->getNameInLayout() . '-' . $item->getId() . '-button';
            $block          = $this->createButton($buttonName, $blockClassName);
            $block->setData($data);
            return $block->toHtml();
        }
        return parent::_toHtml();
    }
}
