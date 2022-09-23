<?php
namespace WeltPixel\GA4\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProductClickEventObserver implements ObserverInterface
{
    /**
     * @var \WeltPixel\GA4\Helper\Data
     */
    protected $helper;

    /**
     * @param \WeltPixel\GA4\Helper\Data $helper
     */
    public function __construct(\WeltPixel\GA4\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isEnabled() || !$this->helper->isProductClickTrackingEnabled()) {
            return $this;
        }

        $productClickHtmlObject = $observer->getData('html');
        $product = $observer->getData('product');
        $productIndex = $observer->getData('index');
        $productListValue = $observer->getData('list');
        $productListId = $observer->getData('listId');
        $html = $productClickHtmlObject->getHtml();
        $html .= $this->helper->getProductClickHtml($product, $productIndex, $productListValue, $productListId);
        $productClickHtmlObject->setHtml($html);

        return $this;
    }
}
