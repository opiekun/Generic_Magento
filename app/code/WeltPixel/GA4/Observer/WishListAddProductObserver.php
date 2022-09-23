<?php

namespace WeltPixel\GA4\Observer;

use Magento\Framework\Event\ObserverInterface;

class WishListAddProductObserver implements ObserverInterface
{
    /**
     * @var \WeltPixel\GA4\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;


    /**
     * @param \WeltPixel\GA4\Helper\Data $helper
     */
    public function __construct(\WeltPixel\GA4\Helper\Data $helper,
                                \Magento\Customer\Model\Session $customerSession)
    {
        $this->helper = $helper;
        $this->customerSession = $customerSession;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isEnabled()) {
            return $this;
        }

        $product = $observer->getData('product');
        $wishlistItem = $observer->getData('item');
        $buyRequest = $wishlistItem->getBuyRequest()->getData();

        $this->customerSession->setGA4AddToWishListData($this->helper->addToWishListPushData($product, $buyRequest, $wishlistItem));

        return $this;
    }
}
