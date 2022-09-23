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

namespace Magezon\TabGrid\Block\Widget\Button;

use Magento\Framework\View\LayoutInterface;

class Toolbar implements ToolbarInterface
{
    /**
     * {@inheritdoc}
     */
    public function pushButtons(
        \Magento\Framework\View\Element\AbstractBlock $context,
        \Magezon\TabGrid\Block\Widget\Button\ButtonList $buttonList
    ) {
        foreach ($buttonList->getItems() as $buttons) {
            /** @var \Magezon\TabGrid\Block\Widget\Button\Item $item */
            foreach ($buttons as $item) {
                $containerName = $context->getNameInLayout() . '-' . $item->getButtonKey();

                $container = $this->createContainer($context->getLayout(), $containerName, $item);

                if ($item->hasData('name')) {
                    $item->setData('element_name', $item->getName());
                }

                if ($container) {
                    $container->setContext($context);
                    $toolbar = $this->getToolbar($context, $item->getRegion());
                    $toolbar->setChild($item->getButtonKey(), $container);
                }
            }
        }
    }

    /**
     * Create button container
     *
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param string $containerName
     * @param \Magezon\TabGrid\Block\Widget\Button\Item $buttonItem
     * @return \Magezon\TabGrid\Block\Widget\Button\Toolbar\Container
     */
    protected function createContainer(LayoutInterface $layout, $containerName, $buttonItem)
    {
        $container = $layout->createBlock(
            '\Magezon\TabGrid\Block\Widget\Button\Toolbar\Container',
            $containerName,
            ['data' => ['button_item' => $buttonItem]]
        );
        return $container;
    }

    /**
     * Return button parent block
     *
     * @param \Magento\Framework\View\Element\AbstractBlock $context
     * @param string $region
     * @return \Magezon\TabGrid\Block\Template
     */
    protected function getToolbar(\Magento\Framework\View\Element\AbstractBlock $context, $region)
    {
        $parent = null;
        $layout = $context->getLayout();
        if (!$region || $region == 'header' || $region == 'footer') {
            $parent = $context;
        } elseif ($region == 'toolbar') {
            $parent = $layout->getBlock('page.actions.toolbar');
        } else {
            $parent = $layout->getBlock($region);
        }

        if ($parent) {
            return $parent;
        }
        return $context;
    }
}
