<?php

namespace WeltPixel\OwlCarouselSlider\Controller\Adminhtml\Banner;

/**
 * Delete Banner action
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class Delete extends \WeltPixel\OwlCarouselSlider\Controller\Adminhtml\Banner
{
    /**
     * Dispatch request
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $bannerId = $this->getRequest()->getParam(static::PARAM_ID);

        try {
            $banner = $this->_bannerFactory->create()->setId($bannerId);
            $banner->delete();
            $this->messageManager->addSuccess(__('Delete successfully!'));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
