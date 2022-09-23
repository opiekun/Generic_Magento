<?php

namespace WeltPixel\OwlCarouselSlider\Model\Config\Source;

class PrevNextDesign implements \Magento\Framework\Option\ArrayInterface
{
    const DESIGN1_CAROUSEL_SIDE = 1;
    const DESIGN2_UNDER_CAROUSEL = 2;
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Design 1  - Carousel sides'),
                'value' => self::DESIGN1_CAROUSEL_SIDE,
            ],
            [
                'label' => __('Design 2 - Under carousel'),
                'value' => self::DESIGN2_UNDER_CAROUSEL,
            ],
        ];
    }
}
