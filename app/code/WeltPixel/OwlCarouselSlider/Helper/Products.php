<?php

namespace WeltPixel\OwlCarouselSlider\Helper;

/**
 * Helper Products Slider
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class Products extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_scopeConfig;

    const SYS_PATH = 'weltpixel_owl_carousel_config/';
    const GENERAL_NAV = '1';
    const GENERAL_DOTS = '1';
    const GENERAL_DOTSEACH = '0';
    const GENERAL_CENTER = '0';
    const GENERAL_ITEMS = '5';
    const GENERAL_STAGE_PADDING = '0';

    /**
     * @param \Magento\Framework\App\Helper\Context  $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);

        $this->_scopeConfig = $context->getScopeConfig();
    }

    /**
     * Retrieve the slider config options.
     *
     * @param $type
     * @return array
     */
    public function getSliderConfigOptions($type)
    {
        $configFields = [
            'status',
            'title',
            'period',
            'show_price',
            'show_addto',
            'show_wishlist',
            'show_compare',
            'show_reviews_ratings',
            'random_sort',
            'loop',
            'margin',
            'slide_by',
            'merge',
            'URLhashListener',
            'lazyLoad',
            'autoplay',
            'autoplayTimeout',
            'autoplayHoverPause',
            'navSpeed',
            'dotsSpeed',
            'rtl',
            'nav_design',
            'nav_prev_label',
            'nav_next_label',

            'nav_brk1',
            'dots_brk1',
            'dotsEach_brk1',
            'items_brk1',
            'center_brk1',
            'stagePadding_brk1',

            'nav_brk2',
            'dots_brk2',
            'dotsEach_brk2',
            'items_brk2',
            'center_brk2',
            'stagePadding_brk2',

            'nav_brk3',
            'dots_brk3',
            'dotsEach_brk3',
            'items_brk3',
            'center_brk3',
            'stagePadding_brk3',

            'nav_brk4',
            'dots_brk4',
            'dotsEach_brk4',
            'items_brk4',
            'center_brk4',
            'stagePadding_brk4',
        ];

        // default general settings
        $sliderConfig = [
            'nav' => self::GENERAL_NAV,
            'dots' => self::GENERAL_DOTS,
            'dotsEach' => self::GENERAL_DOTSEACH,
            'center' => self::GENERAL_CENTER,
            'items' => self::GENERAL_ITEMS,
            'stagePadding' => self::GENERAL_STAGE_PADDING,
        ];

        $sysPath = self::SYS_PATH . $type;
        foreach ($configFields as $field) {
            $configPath = $sysPath . '/' . $field;
            // get default value if setting of current break point is null
            switch ($field) {
                case 'stagePadding_brk1':
                case 'stagePadding_brk2':
                case 'stagePadding_brk3':
                case 'stagePadding_brk4':
                    $configVal = $this->_getConfigValue($configPath);
                    $sliderConfig[$field] = $configVal != NULL ? $configVal : '0';
                    break;
                case 'items_brk1':
                case 'items_brk2':
                case 'items_brk3':
                case 'items_brk4':
                    $configVal = $this->_getConfigValue($configPath);
                    $sliderConfig[$field] = $configVal != NULL ? $configVal : '5';
                default:
                    $sliderConfig[$field] = $this->_getConfigValue($configPath);
                    break;
            }
        }

        return $sliderConfig;
    }

    /**
     * Retrieve the config value.
     *
     * @param string $configPath
     * @return mixed
     */
    private function _getConfigValue($configPath)
    {
        return $this->_scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve the product limit config value.
     *
     * @param string $type
     * @return int
     */
    public function getProductLimit($type)
    {
        $configPath = self::SYS_PATH . $type . '/max_items';

        return (int)$this->_getConfigValue($configPath);
    }

    /**
     * Retrieve the random sort config value.
     * @deprecated
     * @param string $type
     * @return int
     */
    public function getRandomSort($type)
    {
        $configPath = self::SYS_PATH . $type . '/random_sort';

        return $this->_getConfigValue($configPath);
    }

    /**
     * @param string $type
     * @return int
     */
    public function getSortOrder($type)
    {
        $configPath = self::SYS_PATH . $type . '/random_sort';
        return $this->_getConfigValue($configPath);
    }

    /**
     * Retrieve the slider configuration.
     *
     * @param string $type
     * @return array
     */
    public function getSliderConfiguration($type)
    {
        switch($type){
            case 'related':
            case 'related-rule':
                $type = 'related_products';
                break;
            case 'upsell':
            case 'upsell-rule':
                $type = 'upsell_products';
                break;
            case 'crosssell':
                $type = 'crosssell_products';
                break;
            default:
                $type = 'related_products';
        }

        return $this->getSliderConfigOptions($type);
    }
}
