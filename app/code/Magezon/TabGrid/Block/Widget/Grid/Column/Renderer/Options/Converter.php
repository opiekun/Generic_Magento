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

class Converter
{
    /**
     * Convert data from tree format to flat format
     *
     * @param array $treeData
     * @return array
     */
    public function toFlatArray($treeData)
    {
        $options = [];
        if (is_array($treeData)) {
            foreach ($treeData as $item) {
                if (isset($item['value']) && isset($item['label'])) {
                    $options[$item['value']] = $item['label'];
                }
            }
        }
        return $options;
    }

    /**
     * Convert data from flat format to tree format
     *
     * @param array $flatData
     * @return array
     */
    public function toTreeArray($flatData)
    {
        $options = [];
        if (is_array($flatData)) {
            foreach ($flatData as $key => $item) {
                $options[] = ['value' => $key, 'label' => $item];
            }
        }
        return $options;
    }
}
