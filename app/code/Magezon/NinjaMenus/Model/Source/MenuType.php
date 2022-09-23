<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_NinjaMenus
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\NinjaMenus\Model\Source;

class MenuType implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = [
            'label' => __('Horizontal'),
            'value' => 'horizontal',
        ];

        $options[] = [
            'label' => __('Vertical'),
            'value' => 'vertical',
        ];

        $options[] = [
            'label' => __('Accordion'),
            'value' => 'accordion',
        ];

        $options[] = [
            'label' => __('Drilldown'),
            'value' => 'drilldown'
        ];

        return $options;
    }
}
