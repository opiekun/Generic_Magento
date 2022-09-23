<?php

namespace WeltPixel\OwlCarouselSlider\Model\ResourceModel\Banner;

/**
 * Banner Collection
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('WeltPixel\OwlCarouselSlider\Model\Banner',
            'WeltPixel\OwlCarouselSlider\Model\ResourceModel\Banner');
    }
}
