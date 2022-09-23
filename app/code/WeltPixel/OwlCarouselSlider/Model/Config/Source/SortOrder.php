<?php

/**
 * Used in creating options for category config value selection
 *
 */
namespace WeltPixel\OwlCarouselSlider\Model\Config\Source;

class SortOrder implements \Magento\Framework\Option\ArrayInterface
{
    const SORT_DEFAULT = '0';
    const SORT_RANDOM = '1';
    const SORT_ID_ASC = '2';
    const SORT_ID_DESC = '3';
    const SORT_PRICE_ASC = '4';
    const SORT_PRICE_DESC = '5';
    const SORT_NAME_ASC = '6';
    const SORT_NAME_DESC = '7';
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Default'),
                'value' => self::SORT_DEFAULT
            ],
            [
                'label' => __('Random'),
                'value' => self::SORT_RANDOM
            ],
            [
                'label' => __('Product ID Ascending'),
                'value' => self::SORT_ID_ASC
            ],
            [
                'label' => __('Product ID Descending'),
                'value' => self::SORT_ID_DESC
            ],
            [
                'label' => __('Price Ascending '),
                'value' => self::SORT_PRICE_ASC
            ],
            [
                'label' => __('Price Descending '),
                'value' => self::SORT_PRICE_DESC
            ],
            [
                'label' => __('Alphabetically Ascending '),
                'value' => self::SORT_NAME_ASC
            ],
            [
                'label' => __('Alphabetically Descending '),
                'value' => self::SORT_NAME_DESC
            ]

        ];
    }
}
