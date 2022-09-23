<?php

namespace WeltPixel\OwlCarouselSlider\Model\ResourceModel;

/**
 * Banner Resource Model
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class Banner extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * construct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('weltpixel_owlcarouselslider_banners', 'id');
    }
}
