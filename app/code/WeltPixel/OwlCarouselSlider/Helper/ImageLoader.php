<?php

namespace WeltPixel\OwlCarouselSlider\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Class ImageLoader
 * @package WeltPixel\OwlCarouselSlider\Helper
 */
class ImageLoader extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var StoreInterface
     */
    protected $_currentStore;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param StoreInterface $currentStore
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        StoreInterface $currentStore
    )
    {
        parent::__construct($context);

        $this->_scopeConfig = $context->getScopeConfig();
        $this->_currentStore = $currentStore;
    }

    /**
     * @return boolean
     */
    public function useDefaultLoader()
    {
        $sysPath = 'weltpixel_owl_slider_config/general/default_loader';
        return $this->_scopeConfig->getValue($sysPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getLoaderImage()
    {
        $sysPath = 'weltpixel_owl_slider_config/general/loader_image';
        return $this->_scopeConfig->getValue($sysPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $storeId
     * @return string
     */
    public function getLoadingImageUrl()
    {
        $image = $this->getLoaderImage();

        if ($image) {
            $imagePath = 'weltpixel/owlcarouselslider/loaderimage/' . $image;
            $imageUrl = $this->_currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            return $imageUrl . $imagePath;
        } else {
            return '';
        }
    }

}
