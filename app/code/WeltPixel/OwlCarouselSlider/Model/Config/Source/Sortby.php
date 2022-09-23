<?php

/**
 * Used in creating options for category config value selection
 *
 */
namespace WeltPixel\OwlCarouselSlider\Model\Config\Source;

class Sortby implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Default Magento'),
                'value' => '1',
            ],
            [
                'label' => __('Specific Category'),
                'value' => '2',
            ],
            [
                'label' => __('Default Magento & Specific Category'),
                'value' => '3',
            ],

        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            1 => __('Default Magento'),
            2 => __('Specific Category'),
            3 => __('Default Magento & Specific Category'),
        ];
    }
}
