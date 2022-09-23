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

namespace Magezon\TabGrid\Block\Widget\Grid\Column\Filter;

use Magezon\TabGrid\Block\Widget\Grid\Column;

interface FilterInterface
{
    /**
     * @return Column
     * @api
     */
    public function getColumn();

    /**
     * @param Column $column
     * @return AbstractFilter
     * @api
     */
    public function setColumn($column);

    /**
     * @return string
     * @api
     */
    public function getHtml();
}
