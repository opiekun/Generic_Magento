<?php

namespace WeltPixel\OwlCarouselSlider\Controller\Banner;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use WeltPixel\OwlCarouselSlider\Helper\Custom as OwlHelper;
use WeltPixel\OwlCarouselSlider\Model\Slider;

class Validity extends Action
{
    /**
     * @var Slider
     */
    protected $sliderModel;

    /**
     * @var OwlHelper
     */
    protected $owlHelper;

    /**
     * Labels constructor.
     * @param Context $context
     * @param Slider $sliderModel
     * @param OwlHelper $owlHelper
     */
    public function __construct(
        Context $context,
        Slider $sliderModel,
        OwlHelper $owlHelper
    ) {
        $this->sliderModel = $sliderModel;
        $this->owlHelper = $owlHelper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $sliderId = $this->getRequest()->getParam('slider_id');
        $result = [];

        if (!$sliderId) {
            return $this->prepareResult($result);
        }

        $slider = $this->sliderModel->load($sliderId);
        $sliderBannersCollection = $slider->getSliderBanerCollection();
        foreach ($sliderBannersCollection as $banner) {
            if (!$this->owlHelper->validateBannerDisplayDate($banner)) {
                $result['invalidBanners'][] = 'banner-' . $banner->getId();
            }
        }

        return $this->prepareResult($result);
    }

    /**
     * @param array $result
     * @return string
     */
    protected function prepareResult($result)
    {
        $jsonData = json_encode($result);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }
}
