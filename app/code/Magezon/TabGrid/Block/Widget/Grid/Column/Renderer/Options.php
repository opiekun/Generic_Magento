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

class Options extends \Magezon\TabGrid\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * Get options from column
     *
     * @return array
     */
    protected function _getOptions()
    {
        return $this->getColumn()->getOptions();
    }

    /**
     * Render a grid cell as options
     *
     * @param \Magento\Framework\DataObject $row
     * @return string|void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $options = $this->_getOptions();

        $showMissingOptionValues = (bool)$this->getColumn()->getShowMissingOptionValues();
        if (!empty($options) && is_array($options)) {
            //transform option format
            $output = [];
            foreach ($options as $option) {
                $output[$option['value']] = $option['label'];
            }

            $value = $row->getData($this->getColumn()->getIndex());
            if (is_array($value)) {
                $res = [];
                foreach ($value as $item) {
                    if (isset($output[$item])) {
                        $res[] = $this->escapeHtml($output[$item]);
                    } elseif ($showMissingOptionValues) {
                        $res[] = $this->escapeHtml($item);
                    }
                }
                return implode(', ', $res);
            } elseif (isset($output[$value])) {
                return $this->escapeHtml($output[$value]);
            } elseif (in_array($value, $output)) {
                return $this->escapeHtml($value);
            }
        }
    }
}
