<?php

namespace WeltPixel\QuickCart\Plugin\Item\Price;

use WeltPixel\QuickCart\Helper\Data as QuickCartHelper;

class Renderer
{
    /**
     * @var QuickCartHelper
     */
    protected $quickCartHelper;

    /**
     * @param QuickCartHelper $quickCartHelper
     */
    public function __construct(
        QuickCartHelper $quickCartHelper
    ) {
        $this->quickCartHelper = $quickCartHelper;
    }

    /**
     * @param \Magento\Checkout\CustomerData\Cart $subject
     * @param string $template
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSetTemplate(\Magento\Weee\Block\Item\Price\Renderer $subject, $template)
    {
        if ($this->quickCartHelper->isQuickCartDiscountedPriceEnabled() && (strpos($template, 'sidebar.phtml')  !== false)) {
            $template = 'WeltPixel_QuickCart::checkout/cart/item/price/sidebar.phtml';
        }
        return [$template];
    }
}
