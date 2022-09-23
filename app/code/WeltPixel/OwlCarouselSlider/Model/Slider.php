<?php

namespace WeltPixel\OwlCarouselSlider\Model;

/**
 * Slider Model
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class Slider extends \Magento\Framework\Model\AbstractModel
{
    const SLIDER_TYPE_CONFIGURABLE  = 1;
    const SLIDER_TYPE_CUSTOM        = 2;

    const XML_CONFIG_ENABLE_BANNER = 'weltpixel_owl_carouselslider_general/general/enable_owlcarousel';

    /**
     * banner collection factory.
     *
     * @var \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Banner\CollectionFactory
     */
    protected $_bannerCollectionFactory;

    /**
     * constructor.
     *
     * @param \Magento\Framework\Model\Context                                          $context
     * @param \Magento\Framework\Registry                                               $registry
     * @param \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Banner\CollectionFactory $bannerCollectionFactory
     * @param \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Slider                   $resource
     * @param \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Slider\Collection        $resourceCollection
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Banner\CollectionFactory $bannerCollectionFactory,
        \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Slider $resource,
        \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Slider\Collection $resourceCollection
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection);

        $this->_bannerCollectionFactory = $bannerCollectionFactory;
    }

    /**
     * Retrieve available slider type.
     *
     * @return []
     */
    public static function getAvailableTransition()
    {
        return [
            'slide'   => __('Slide'),
            'fadeOut' => __('Fade'),
        ];
    }

    /**
     * Retrieve banner collection of slider.
     *
     * @return \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Banner\Collection
     */
    public function getSliderBanerCollection()
    {
        $bannerCollection = $this->_bannerCollectionFactory->create()->addFieldToFilter('slider_id', array('like' => '%' . $this->getId() . '%'));
        // remove unwanted banners
        foreach ($bannerCollection as $key => $banner) {
            $sliderIds = explode(',', $banner->getSliderId());
            if (!in_array($this->getId(), $sliderIds)) {
                $bannerCollection->removeItemByKey($key);
            }
        }

        return $bannerCollection;
    }
}
