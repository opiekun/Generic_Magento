<?php

/**
 * Used in creating options for category config value selection
 *
 */
namespace WeltPixel\OwlCarouselSlider\Model\Config\Source;

class BestSellPeriod implements \Magento\Framework\Option\ArrayInterface
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
                'label' => __('All Time'),
                'value' => 'beginning',
            ],
            [
                'label' => __('Last Day'),
                'value' => 'last_day',
            ],
            [
                'label' => __('Last Week'),
                'value' => 'last_week',
            ],
            [
                'label' => __('Last Month'),
                'value' => 'last_month',
            ],
            [
                'label' => __('Last Year'),
                'value' => 'last_year',
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
            'beginning' => __('All Time'),
            'last_day' => __('Last Day'),
            'last_week' => __('Last Week'),
            'last_month' => __('Last Month'),
            'last_year' => __('Last Year'),
        ];
    }
}
