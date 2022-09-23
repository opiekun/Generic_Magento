<?php

namespace WeltPixel\OwlCarouselSlider\Controller\Adminhtml\Banner;

/**
 * Edit Banner action.
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class Edit extends \WeltPixel\OwlCarouselSlider\Controller\Adminhtml\Banner
{
    /**
     * Dispatch request
     */
    public function execute()
    {
        $bannerId = $this->getRequest()->getParam('id');

        $bannerModel = $this->_bannerFactory->create();

        if ($bannerId) {
            $bannerModel->load($bannerId);

            if (!$bannerModel->getId()) {
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $formData = $this->_getSession()->getFormData(true);
        if (!empty($formData)) {
            $bannerModel->setData($formData);
        }
      
        $this->_coreRegistry->register('banner', $bannerModel);

        $this->_eventManager->dispatch(
            'owlcarouselslider_edit_banner',
            ['controller' => $this]
        );

        /** @var \Magento\Framework\View\Result\PageFactory $resultPage */
        $resultPage = $this->_resultPageFactory->create();

        return $resultPage;
    }
}
