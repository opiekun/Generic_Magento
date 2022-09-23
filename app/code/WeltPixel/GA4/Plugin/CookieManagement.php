<?php

namespace WeltPixel\GA4\Plugin;

class CookieManagement
{
    /**
     * @var \WeltPixel\GA4\Helper\Data
     */
    protected $helper;

    /**
     * @var \WeltPixel\GA4\Model\CookieManager
     */
    protected $cookieManager;

    /**
     * @param \WeltPixel\GA4\Helper\Data $helper
     * @param \WeltPixel\GA4\Model\CookieManager $cookieManager
     */
    public function __construct(
        \WeltPixel\GA4\Helper\Data $helper,
        \WeltPixel\GA4\Model\CookieManager $cookieManager
    )
    {
        $this->helper = $helper;
        $this->cookieManager = $cookieManager;
    }

    /**
     * @param \Magento\Framework\App\FrontController $subject
     * @param $result
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDispatch(
        \Magento\Framework\App\FrontController $subject,
        $result
    ) {
        if (!$this->helper->isEnabled()) {
            return $result;
        }

        $this->cookieManager->setGA4Cookies();
        return $result;

    }
}
