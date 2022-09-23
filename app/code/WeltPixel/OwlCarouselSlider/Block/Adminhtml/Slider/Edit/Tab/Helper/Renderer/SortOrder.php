<?php

namespace WeltPixel\OwlCarouselSlider\Block\Adminhtml\Slider\Edit\Tab\Helper\Renderer;

/**
 * Edit banner form
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class SortOrder extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * banner factory.
     *
     * @var \WeltPixel\OwlCarouselSlider\Model\BannerFactory
     */
    protected $_bannerFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Serialize
     */
    protected $_serializer;

    /**
     * [__construct description].
     *
     * @param \Magento\Backend\Block\Context                        $context
     * @param \Magento\Store\Model\StoreManagerInterface            $storeManager
     * @param \WeltPixel\OwlCarouselSlider\Model\BannerFactory      $bannerFactory
     * @param \Magento\Framework\Serialize\Serializer\Serialize     $serializer
     * @param array                                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \WeltPixel\OwlCarouselSlider\Model\BannerFactory $bannerFactory,
        \Magento\Framework\Serialize\Serializer\Serialize $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->_bannerFactory = $bannerFactory;
        $this->_serializer = $serializer;
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
        $sliderId = $this->getRequest()->getParam('id');
        $sortOrder = false;
        try {
            $sortOrder = $this->_serializer->unserialize($row->getSortOrder());
        } catch (\Exception $ex) {}
        if ($sortOrder !== false && isset($sortOrder[$sliderId])) {
            $sortOrder = $sortOrder[$sliderId];
        } else {
            $sortOrder = 0;
        }

        $input = '<input type="text" class="input-text " name="sort_order" value="' . $sortOrder . '" tabindex="">';
        $input .= '<p>' . __('Make sure this value is not duplicated') . '</p>';

        return $input;
    }
}
