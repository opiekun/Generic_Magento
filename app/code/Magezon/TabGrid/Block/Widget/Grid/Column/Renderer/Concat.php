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

namespace Magezon\TabGrid\Block\Widget\Grid\Column\Renderer;

class Concat extends \Magezon\TabGrid\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renders grid column
     *
     * @param   \Magento\Framework\DataObject $row
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $dataArr = [];
        $column = $this->getColumn();
        $methods = $column->getGetter() ?: $column->getIndex();
        foreach ($methods as $method) {
            if ($column->getGetter()
                && is_callable([$row, $method])
                && substr_compare('get', $method, 1, 3) !== 0
            ) {
                $data = call_user_func([$row, $method]);
            } else {
                $data = $row->getData($method);
            }
            if (strlen($data) > 0) {
                $dataArr[] = $data;
            }
        }
        $data = implode($column->getSeparator(), $dataArr);

        return $data;
    }
}
