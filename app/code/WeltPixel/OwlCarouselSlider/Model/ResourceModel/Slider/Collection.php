<?php

namespace WeltPixel\OwlCarouselSlider\Model\ResourceModel\Slider;

/**
 * Slider Collection
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * _contruct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('WeltPixel\OwlCarouselSlider\Model\Slider',
            'WeltPixel\OwlCarouselSlider\Model\ResourceModel\Slider');
    }
}
