<?php
namespace WeltPixel\OwlCarouselSlider\Block\Slider;

use Magento\Framework\App\Filesystem\DirectoryList;

class Custom extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected $_sliderId;
    protected $_sliderConfiguration;
    protected $_helperCustom;
    protected $_mobileHelperData;
    protected $_filterProvider;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * Custom constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \WeltPixel\OwlCarouselSlider\Helper\Custom $helperCustom
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \WeltPixel\MobileDetect\Helper\Data $mobileHelperData
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \WeltPixel\OwlCarouselSlider\Helper\Custom $helperCustom,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \WeltPixel\MobileDetect\Helper\Data $mobileHelperData,
        \Magento\Framework\Filesystem $fileSystem,
        array $data = []
    ) {
        $this->_helperCustom = $helperCustom;
        $this->_mobileHelperData = $mobileHelperData;
        $this->_filterProvider = $filterProvider;
        $this->_filesystem = $fileSystem;
        $this->setTemplate('sliders/custom.phtml');
        parent::__construct($context, $data);
    }

    /**
     * @param $video
     * @return mixed
     */
    public function getVideoHtml($video)
    {
        $storeId = $this->_storeManager->getStore()->getId();

        return $this->_filterProvider->getBlockFilter()->setStoreId($storeId)->filter($video);
    }

    public function getSliderConfiguration()
    {
        $sliderId = $this->getData('slider_id');

        if ($this->_sliderId != $sliderId) {
            $this->_sliderId = $sliderId;
        }

        if (is_null($this->_sliderConfiguration)) {
            $this->_sliderConfiguration = $this->_helperCustom->getSliderConfigOptions($this->_sliderId);
        }

        return $this->_sliderConfiguration;
    }

    /**
     * @return array
     */
    public function getBreakpointConfiguration()
    {
        return $this->_helperCustom->getBreakpointConfiguration();
    }

    /**
     * @return mixed
     */
    public function getMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @param $imageName
     * @return array
     */
    public function getImageDetails($imageName)
    {
        $mediapath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        //phpcs:ignore Generic.PHP.NoSilencedErrors
        list($width, $height) = @getimagesize($mediapath . $imageName);

        return [
            'width' => $width,
            'height' => $height,
        ];
    }

    /**
     * @return mixed
     */
    public function isGatEnabled()
    {
        return $this->_helperCustom->isGatEnabled();
    }

    /**
     * @return mixed
     */
    public function getMobileBreakPoint()
    {
        return $this->_helperCustom->getMobileBreakpoint();
    }

    /**
     * @return bool
     */
    public function isMobile()
    {
        return $this->_mobileHelperData->isMobile();
    }
}
