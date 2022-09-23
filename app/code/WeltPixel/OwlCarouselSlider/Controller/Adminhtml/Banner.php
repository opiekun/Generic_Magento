<?php

namespace WeltPixel\OwlCarouselSlider\Controller\Adminhtml;

/**
 * Banner Abstract Action
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
abstract class Banner extends \WeltPixel\OwlCarouselSlider\Controller\Adminhtml\AbstractAction
{
    const PARAM_ID = 'id';

    /**
     * Check if admin has permissions to visit banner pages.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('WeltPixel_OwlCarouselSlider::owlcarouselslider_custom_banners');
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
        switch ($this->getRequest()->getParam('back')) {
            case 'new':
                $resultRedirect->setPath('*/*/new', ['_current' => true]);
                break;
            case 'edit':
                $resultRedirect->setPath(
                    '*/*/edit',
                    [
                        static::PARAM_ID   => $paramId,
                        '_current'         => true,
                        'loaded_slider_id' => $this->getRequest()->getParam('loaded_slider_id'),
                        'saveandclose'     => $this->getRequest()->getParam('saveandclose'),
                    ]
                );
                break;
            default:
                $resultRedirect->setPath('*/*/');
        }

        return $resultRedirect;
    }
}
