<?php

namespace WeltPixel\OwlCarouselSlider\Block\Adminhtml\Banner\Helper\Renderer;

/**
 * Image renderer.
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class ThumbImage extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Banner factory.
     *
     * @var \WeltPixel\OwlCarouselSlider\Model\BannerFactory
     */
    protected $_bannerFactory;

    /**
     * [__construct description].
     *
     * @param \Magento\Backend\Block\Context                    $context
     * @param \Magento\Store\Model\StoreManagerInterface        $storeManager
     * @param \WeltPixel\OwlCarouselSlider\Model\BannerFactory  $bannerFactory
     * @param array                                             $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \WeltPixel\OwlCarouselSlider\Model\BannerFactory $bannerFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_storeManager  = $storeManager;
        $this->_bannerFactory = $bannerFactory;
    }

    /**
     * Render action.
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $banner = $this->_bannerFactory->create()->load($row->getId());

        if ($banner->getThumbImage()) {
            $imageSrc = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                . $banner->getThumbImage();

            return '<image width="150" src ="' . $imageSrc . '" alt="' . $banner->getThumbImage() . '" />';
        }

        return '';
    }
}
