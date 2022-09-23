<?php

namespace WeltPixel\OwlCarouselSlider\Controller\Adminhtml;

/**
 * Abstract Action
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
abstract class AbstractAction extends \Magento\Backend\App\Action
{
    const PARAM_ID = 'id';

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $_jsHelper;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $_resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_resultLayoutFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Banner factory.
     *
     * @var \WeltPixel\OwlCarouselSlider\Model\BannerFactory
     */
    protected $_bannerFactory;

    /**
     * Slider factory.
     *
     * @var \WeltPixel\OwlCarouselSlider\Model\SliderFactory
     */
    protected $_sliderFactory;

    /**
     * Banner Collection Factory.
     *
     * @var \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Banner\CollectionFactory
     */
    protected $_bannerCollectionFactory;

    /**
     * Slider Collection Factory.
     *
     * @var \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Slider\CollectionFactory
     */
    protected $_sliderCollectionFactory;

    /**
     * Registry object.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * File Factory.
     *
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Serialize
     */
    protected $_serializer;

    /**
     * @param \Magento\Backend\App\Action\Context                                        $context
     * @param \Magento\Framework\Registry                                                $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory                           $fileFactory
     * @param \Magento\Framework\View\Result\PageFactory                                 $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory                               $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory                          $resultForwardFactory
     * @param \Magento\Backend\Helper\Js                                                 $jsHelper
     * @param \WeltPixel\OwlCarouselSlider\Model\BannerFactory                           $bannerFactory
     * @param \WeltPixel\OwlCarouselSlider\Model\SliderFactory                           $sliderFactory
     * @param \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Banner\CollectionFactory  $bannerCollectionFactory
     * @param \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Slider\CollectionFactory  $sliderCollectionFactory
     * @param \Magento\Framework\Serialize\Serializer\Serialize                          $serializer
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Backend\Helper\Js $jsHelper,
        \WeltPixel\OwlCarouselSlider\Model\BannerFactory $bannerFactory,
        \WeltPixel\OwlCarouselSlider\Model\SliderFactory $sliderFactory,
        \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Banner\CollectionFactory $bannerCollectionFactory,
        \WeltPixel\OwlCarouselSlider\Model\ResourceModel\Slider\CollectionFactory $sliderCollectionFactory,
        \Magento\Framework\Serialize\Serializer\Serialize $serializer
    ) {
        parent::__construct($context);

        $this->_coreRegistry = $coreRegistry;
        $this->_fileFactory = $fileFactory;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->_jsHelper = $jsHelper;
        $this->_bannerFactory = $bannerFactory;
        $this->_sliderFactory = $sliderFactory;
        $this->_bannerCollectionFactory = $bannerCollectionFactory;
        $this->_sliderCollectionFactory = $sliderCollectionFactory;
        $this->_serializer = $serializer;
    }

    /**
     * Get result redirect after add/edit action
     *
     * @param \Magento\Framework\Controller\Result\Redirect $resultRedirect
     * @param null                                          $paramCrudId
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function _getResultRedirect(\Magento\Framework\Controller\Result\Redirect $resultRedirect, $paramId = null)
    {
        $back = $this->getRequest()->getParam('back');

        switch ($back) {
            case 'new':
                $resultRedirect->setPath('*/*/new', ['_current' => true]);
                break;
            case 'edit':
                $resultRedirect->setPath('*/*/edit', ['id' => $paramId, '_current' => true]);
                break;
            default:
                $resultRedirect->setPath('*/*/');
        }

        return $resultRedirect;
    }
}
