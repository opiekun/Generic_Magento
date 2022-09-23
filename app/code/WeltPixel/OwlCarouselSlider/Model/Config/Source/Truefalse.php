<?php

/**
 * Used in creating options for true/false config value selection
 *
 */
namespace WeltPixel\OwlCarouselSlider\Model\Config\Source;

class Truefalse implements \Magento\Framework\Option\ArrayInterface
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
                'label' => __('True'),
                'value' => '1',
            ],
            [
                'label' => __('False'),
                'value' => '0',
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
            1 => __('True'),
            0 => __('False'),
        ];
    }
}
