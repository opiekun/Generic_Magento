<?php

namespace WeltPixel\OwlCarouselSlider\Helper;

/**
 * Helper Custom Slider
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class Custom extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Slider factory.
     *
     * @var \WeltPixel\OwlCarouselSlider\Model\Slider
     */
    protected $_sliderModel;

    protected $_configFieldsSlider;

    protected $_configFieldsBanner;

    protected $_sliderId;

    protected $_date;

    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Serialize
     */
    protected $_serializer;

    /**
     * Custom constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \WeltPixel\OwlCarouselSlider\Model\Slider $sliderModel
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Serialize\Serializer\Serialize $serializer
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \WeltPixel\OwlCarouselSlider\Model\Slider $sliderModel,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Serialize\Serializer\Serialize $serializer
    ) {
        parent::__construct($context);

        $this->_sliderModel = $sliderModel;
        $this->_date        = $date;
        $this->_serializer  = $serializer;
        $this->_scopeConfig = $context->getScopeConfig();
    }

    /**
     * Retrieve the slider config options.
     *
     * @param $sliderId
     * @return \Magento\Framework\DataObject
     */
    public function getSliderConfigOptions($sliderId)
    {
        if ($this->_sliderId != $sliderId && is_null($this->_configFieldsSlider)) {
            $this->_sliderId = $sliderId;
            $this->_configFieldsSlider = [
                'title',
                'show_title',
                'status',
                'scheduled_ajax',
                'nav',
                'dots',
                'dotsEach',
                'thumbs',
                'center',
                'items',
                'loop',
                'margin',
                'merge',
                'URLhashListener',
                'wrap_link',
                'stagePadding',
                'lazyLoad',
                'transition',
                'autoplay',
                'autoplayTimeout',
                'autoplayHoverPause',
                'autoHeight',
                'navSpeed',
                'dotsSpeed',
                'rtl',
                'nav_brk1',
                'items_brk1',
                'nav_brk2',
                'items_brk2',
                'nav_brk3',
                'items_brk3',
                'nav_brk4',
                'items_brk4',
            ];
        }
        if (is_null($this->_configFieldsBanner)) {
            $this->_configFieldsBanner = [
                'id',
                'title',
                'show_title',
                'description',
                'show_description',
                'status',
                'url',
                'wrap_link',
                'banner_type',
                'image',
                'mobile_image',
                'thumb_image',
                'video',
                'custom',
                'alt_text',
                'target',
                'button_text',
                'custom_content',
                'custom_css',
                'valid_from',
                'valid_to',
                'ga_promo_id',
                'ga_promo_name',
                'ga_promo_creative',
                'ga_promo_position'
            ];
        }

        /* @var \WeltPixel\OwlCarouselSlider\Model\Slider $slider */
        $slider = $this->_sliderModel->load($sliderId);

        if (!count($this->_configFieldsSlider)) {
            return new \Magento\Framework\DataObject();
        }

        $sliderConfig = [];
        foreach ($this->_configFieldsSlider as $field) {
            $sliderConfig[$field] = $slider->getData($field);
        }

        $sliderBannersCollection = $slider->getSliderBanerCollection();
        // $sliderBannersCollection->setOrder('sort_order', 'ASC');

        $enableAjaxSchedule = $sliderConfig['scheduled_ajax'];

        $banners = [];
        foreach ($sliderBannersCollection as $banner) {
            if (!$banner->getStatus()) {
                continue;
            }

            if (!$enableAjaxSchedule && !$this->validateBannerDisplayDate($banner)) {
                continue;
            }

            // reorder banners
            $sortOrder = false;
            try {
                $sortOrder = $this->_serializer->unserialize($banner->getSortOrder());
            } catch (\Exception $ex) {}
            if ($sortOrder === false) {
                $sortOrder = $banner->getSortOrder();
            } else {
                $sortOrder = $sortOrder[$sliderId];
            }

            if (isset($banners[$sortOrder])) {
                $sortOrder = $this->_incrementSortOrder($sortOrder, $banners);
            }

            $banners[$sortOrder] = $banner;
        }

        ksort($banners);

        $bannerConfig = [];
        foreach ($banners as $banner) {
            $bannerDetails = [];
            foreach ($this->_configFieldsBanner as $field) {
                $bannerDetails[$field] = $banner->getData($field);
            }
            $bannerConfig[$banner->getId()] = $bannerDetails;
        }

        $configData = new \Magento\Framework\DataObject();

        $configData->setSliderConfig($sliderConfig);
        $configData->setBannerConfig($bannerConfig);

        return $configData;
    }

    /**
     * @param $sortOrder
     * @param $banners
     * @return mixed
     */
    private function _incrementSortOrder($sortOrder, $banners)
    {
        $sortOrder++;
        if (array_key_exists($sortOrder, $banners)) {
            $sortOrder = $this->_incrementSortOrder($sortOrder, $banners);
        }

        return $sortOrder;
    }

    /**
     * Retrieve the breakpoint configuration.
     *
     * @return array
     */
    public function getBreakpointConfiguration()
    {
        $configPaths = [
            'breakpoint_1',
            'breakpoint_2',
            'breakpoint_3',
            'breakpoint_4',
        ];

        $breakpointConfiguration = [];

        foreach ($configPaths as $configPath) {
            $value = $this->_getConfigValue($configPath);
            $breakpointConfiguration[$configPath] = $value ? $value : 0;
        }

        return $breakpointConfiguration;
    }

    /**
     * Retrieve the breakpoint configuration.
     *
     * @return array
     */
    public function getDisplaySocial()
    {
        $configPaths = [
            'display_wishlist',
            'display_compare'
        ];

        $displaySocial = [];

        foreach ($configPaths as $configPath) {
            $value = $this->_getConfigValue($configPath);
            $displaySocial[$configPath] = $value ? $value : 0;
        }

        return $displaySocial;
    }

    /**
     * Retrieve the config value.
     *
     * @param string $configPath
     * @return mixed
     */
    private function _getConfigValue($configPath)
    {
        $sysPath = 'weltpixel_owl_carousel_config/general/' . $configPath;

        return $this->_scopeConfig->getValue($sysPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Validate the banner display date.
     *
     * @param \WeltPixel\OwlCarouselSlider\Model\Banner $banner
     * @return bool
     */
    public function validateBannerDisplayDate($banner)
    {
        $validFrom = $banner->getValidFrom();
        $validTo   = $banner->getValidTo();

        $now = $this->_date->gmtDate();

        if ($validFrom <= $now && $validTo >= $now) {
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function isGatEnabled()
    {
        $sysPath = 'weltpixel_owl_slider_config/general/enable_google_tracking';
        return $this->_scopeConfig->getValue($sysPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function isHoverImageEnabled()
    {
        $sysPath = 'weltpixel_owl_slider_config/general/enable_hover_image';
        return $this->_scopeConfig->getValue($sysPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getMobileBreakpoint()
    {
        $sysPath = 'weltpixel_owl_slider_config/general/mobile_breakpoint';
        return $this->_scopeConfig->getValue($sysPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
