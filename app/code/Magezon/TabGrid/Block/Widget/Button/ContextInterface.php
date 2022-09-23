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

interface ContextInterface
{
    /**
     * Check whether button rendering is allowed in current context
     *
     * @param \Magezon\TabGrid\Block\Widget\Button\Item $item
     * @return bool
     * @api
     */
    public function canRender(\Magezon\TabGrid\Block\Widget\Button\Item $item);
}
