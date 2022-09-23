<?php
namespace WeltPixel\OwlCarouselSlider\Model\Config\Source;

class WidgetProducts implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => __('Select Products Slider...')],
            ['value' => 'new_products', 'label' => __('New Products')],
            ['value' => 'bestsell_products', 'label' => __('Best-sell Products')],
            ['value' => 'sell_products', 'label' => __('Sale Products')],
            ['value' => 'recently_viewed', 'label' => __('Recently Viewed')]
        ];
    }
}
