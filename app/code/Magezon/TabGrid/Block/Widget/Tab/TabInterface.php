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

namespace Magezon\TabGrid\Block\Widget\Tab;

interface TabInterface
{
    /**
     * Return Tab label
     *
     * @return string
     * @api
     */
    public function getTabLabel();

    /**
     * Return Tab title
     *
     * @return string
     * @api
     */
    public function getTabTitle();

    /**
     * Can show tab in tabs
     *
     * @return boolean
     * @api
     */
    public function canShowTab();

    /**
     * Tab is hidden
     *
     * @return boolean
     * @api
     */
    public function isHidden();
}
