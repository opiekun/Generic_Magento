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

class Massaction extends \Magezon\TabGrid\Block\Widget\Grid\Column\Renderer\Checkbox
{
    /**
     * @var int
     */
    protected $_defaultWidth = 20;

    /**
     * Render header of the row
     *
     * @return string
     */
    public function renderHeader()
    {
        return '&nbsp;';
    }

    /**
     * Render HTML properties
     *
     * @return string
     */
    public function renderProperty()
    {
        $out = parent::renderProperty();
        $out = preg_replace('/class=".*?"/i', '', $out);
        $out .= ' class="a-center"';
        return $out;
    }

    /**
     * Returns HTML of the object
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($this->getColumn()->getGrid()->getMassactionIdFieldOnlyIndexValue()) {
            $this->setNoObjectId(true);
        }
        return parent::render($row);
    }

    /**
     * Returns HTML of the checkbox
     *
     * @param string $value
     * @param bool   $checked
     * @return string
     */
    protected function _getCheckboxHtml($value, $checked)
    {
        $id = 'id_' . rand(0, 999);
        $html = '<label class="data-grid-checkbox-cell-inner" for="'. $id .'">';
        $html .= '<input type="checkbox" name="' . $this->getColumn()->getName() . '" ';
        $html .= 'id="' . $id . '" data-role="select-row"';
        $html .= 'value="' . $this->escapeHtml($value) . '" class="mgz__control-checkbox"' . $checked . '/>';
        $html .= '<label for="'. $id .'"></label></label>';
        return $html;
    }
}
