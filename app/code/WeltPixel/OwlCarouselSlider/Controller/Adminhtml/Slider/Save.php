<?php

namespace WeltPixel\OwlCarouselSlider\Controller\Adminhtml\Slider;

use WeltPixel\OwlCarouselSlider\Model\Slider;

/**
 * Save Slider action
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class Save extends \WeltPixel\OwlCarouselSlider\Controller\Adminhtml\Slider
{
    /**
     * @return $this|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $formPostValues = $this->getRequest()->getPostValue();

        if (isset($formPostValues['slider'])) {
            $sliderData = $formPostValues['slider'];
            $sliderId = isset($sliderData['id']) ? $sliderData['id'] : null;

            $sliderModel = $this->_sliderFactory->create();

            $sliderModel->load($sliderId);

            $sliderModel->setData($sliderData);

            try {
                $sliderModel->save();

                if (isset($formPostValues['slider_banner'])) {
                    $bannerGridSerializedInputData = $this->_jsHelper->decodeGridSerializedInput($formPostValues['slider_banner']);
                    $bannerIds = [];
                    $bannerOrders = [];
                    foreach ($bannerGridSerializedInputData as $key => $value) {
                        $bannerIds[] = $key;
                        $bannerOrders[] = $value['sort_order'];
                    }
                    $unSelecteds = $this->_bannerCollectionFactory
                        ->create()
                        ->addFieldToFilter('slider_id', ['like' => '%' . $sliderModel->getId() . '%']);
                    ;

                    // remove unwanted banners
                    foreach ($unSelecteds as $key => $banner) {
                        $sliderIds = explode(',', $banner->getSliderId());
                        if (!in_array($sliderModel->getId(), $sliderIds)) {
                            $unSelecteds->removeItemByKey($key);
                        }
                    }

                     if (count($bannerIds)) {
                         $unSelecteds->addFieldToFilter('id', ['nin' => $bannerIds]);
                     }

                    foreach ($unSelecteds as $banner) {
                        if (in_array($banner->getId(), $bannerIds)) continue;

                        $sliderIds = explode(',', $banner->getSliderId());
                        if (in_array($sliderModel->getId(), $sliderIds)) {
                            $key = array_search($sliderModel->getId(), $sliderIds);
                            array_splice($sliderIds, $key, 1);
                        }
                        $sliderIds = count($sliderIds) ? implode(',', $sliderIds) : 0;

                        $currentBannerOrders = false;
                        try {
                            $currentBannerOrders = $this->_serializer->unserialize($banner->getSortOrder());
                        } catch (\Exception $ex) {}
                        if ($currentBannerOrders === false) {
                            // make it array
                            if ($banner->getSortOrder() != 0) {
                                $currentBannerOrders = [
                                    $sliderModel->getId() => $banner->getSortOrder()
                                ];
                            }
                        }
                        if (isset($currentBannerOrders[$sliderModel->getId()])) {
                            unset($currentBannerOrders[$sliderModel->getId()]);
                        }

                        if (count($currentBannerOrders)) {
                            $currentBannerOrders = $this->_serializer->serialize($currentBannerOrders);
                        } else {
                            $currentBannerOrders = 0;
                        }

                        $banner
                            ->setSliderId($sliderIds)
                            ->setSortOrder($currentBannerOrders)
                            ->save();
                    }

                    $selectBanner = $this->_bannerCollectionFactory
                        ->create()
                        ->addFieldToFilter('id', ['in' => $bannerIds])
                    ;

                    $i = -1;
                    foreach ($selectBanner as $banner) {
                        $sliderIds = explode(',', $banner->getSliderId());
                        foreach ($sliderIds as $key => $id) {
                            if ($id == 0)  unset($sliderIds[$key]);
                        }
                        if (!in_array($sliderModel->getId(), $sliderIds)) {
                            $sliderIds[] = $sliderModel->getId();
                        }
                        $sliderIds = implode(',', $sliderIds);

                        $currentBannerOrders = false;
                        try {
                            $currentBannerOrders = $this->_serializer->unserialize($banner->getSortOrder());
                        } catch (\Exception $ex) {}
                        if ($currentBannerOrders === false) {
                            // make it array
                            if ($banner->getSortOrder() != 0) {
                                $currentBannerOrders = [
                                    $banner->getSliderId() => $banner->getSortOrder()
                                ];
                            }
                        }
                        $currentBannerOrders[$sliderModel->getId()] = $bannerOrders[++$i];
                        $currentBannerOrders = $this->_serializer->serialize($currentBannerOrders);

                        $banner
                            ->setSliderId($sliderIds)
                            ->setSortOrder($currentBannerOrders)
                            ->save();
                    }
                }

                $this->messageManager->addSuccess(__('The slider has been saved.'));
                $this->_getSession()->setFormData(false);

                return $this->_getResultRedirect($resultRedirect, $sliderModel->getId());
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addException($e, __('Something went wrong while saving the slider.'));
            }

            $this->_getSession()->setFormData($formPostValues);

            return $resultRedirect->setPath('*/*/edit', [static::PARAM_ID => $sliderId]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
