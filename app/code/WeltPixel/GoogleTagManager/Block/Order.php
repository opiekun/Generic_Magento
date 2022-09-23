<?php
namespace WeltPixel\GoogleTagManager\Block;

/**
 * Class \WeltPixel\GoogleTagManager\Block\Order
 */
class Order extends \WeltPixel\GoogleTagManager\Block\Core
{
    /**
     * Returns the product details for the purchase gtm event
     * @return array
     */
    public function getProducts() {
        $order = $this->getOrder();
        $products = [];

        $displayOption = $this->helper->getParentOrChildIdUsage();

        foreach ($order->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            $productIdModel = $product;
            if ($displayOption == \WeltPixel\GoogleTagManager\Model\Config\Source\ParentVsChild::CHILD) {
                if ($item->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                    $children = $item->getChildrenItems();
                    foreach ($children as $child) {
                        $productIdModel = $child->getProduct();
                    }
                }
            }

            $productDetail = [];
            $productDetail['name'] = html_entity_decode($item->getName());
            $productDetail['id'] = $this->helper->getGtmProductId($productIdModel); //$this->helper->getGtmOrderItemId($item);
            $productDetail['price'] = number_format($item->getPrice(), 2, '.', '');
            if ($this->helper->isBrandEnabled()) {
                $productDetail['brand'] = $this->helper->getGtmBrand($product);
            }
            if ($this->helper->isVariantEnabled()) {
                $productOptions = $item->getData('product_options');
                $productType = $item->getData('product_type');
                $variant = $this->helper->checkVariantForProductOptions($productOptions, $productType);
                if ($variant) {
                    $productDetail['variant'] = $variant;
                }
            }

            $categoryName = $this->helper->getGtmCategoryFromCategoryIds($product->getCategoryIds());
            $productDetail['category'] = $categoryName;
            $productDetail['list'] = $categoryName;
            $productDetail['quantity'] = $item->getQtyOrdered();

            /**  Set the custom dimensions */
            $customDimensions = $this->getProductDimensions($product);
            foreach ($customDimensions as $name => $value) :
                $productDetail[$name] = $value;
            endforeach;

            $products[] = $productDetail;
        }

        return $products;
    }

    /**
     * Returns the product id's
     * @return array
     */
    public function getProductIds() {
        $order = $this->getOrder();
        $products = [];

        $displayOption = $this->helper->getParentOrChildIdUsage();

        foreach ($order->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            if ($displayOption == \WeltPixel\GoogleTagManager\Model\Config\Source\ParentVsChild::CHILD) {
                if ($item->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                    $children = $item->getChildrenItems();
                    foreach ($children as $child) {
                        $product = $child->getProduct();
                    }
                }
            }

            $products[] = $this->helper->getGtmProductId($product); //$this->helper->getGtmOrderItemId($item);
        }

        return $products;
    }


    /**
     * Retuns the order total (subtotal or grandtotal)
     * @return float
     */
    public function getOrderTotal() {
        $orderTotalCalculationOption = $this->helper->getOrderTotalCalculation();
        $order =  $this->getOrder();
        switch ($orderTotalCalculationOption) {
            case \WeltPixel\GoogleTagManager\Model\Config\Source\OrderTotalCalculation::CALCULATE_SUBTOTAL :
                $orderTotal = $order->getSubtotal();
                break;
            case \WeltPixel\GoogleTagManager\Model\Config\Source\OrderTotalCalculation::CALCULATE_GRANDTOTAL :
            default:
                $orderTotal = $order->getGrandtotal();
                if ($this->excludeTaxFromTransaction()) {
                    $orderTotal -= $order->getTaxAmount();
                }

                if ($this->excludeShippingFromTransaction()) {
                    $orderTotal -= $order->getShippingAmount();
                    if ($this->excludeShippingFromTransactionIncludingTax()) {
                        $orderTotal -= $order->getShippingTaxAmount();
                    }
                }
                break;
        }

        return $orderTotal;
    }

    /**
     * @return bool
     */
    public function isFreeOrderTrackingAllowedForGoogleAnalytics() {
        $excludeFreeOrder = $this->helper->excludeFreeOrderFromPurchaseForGoogleAnalytics();
        return $this->isFreeOrderAllowed($excludeFreeOrder);
    }

    /**
     * @return bool
     */
    public function isFreeOrderAllowedForAdwordsConversionTracking() {
        $excludeFreeOrder = $this->helper->excludeFreeOrderFromAdwordsConversionTracking();
        return $this->isFreeOrderAllowed($excludeFreeOrder);
    }

    /**
     * @return bool
     */
    public function isFreeOrderAllowedForAdwordsRemarketing() {
        $excludeFreeOrder = $this->helper->excludeFreeOrderFromAdwordsRemarketing();
        return $this->isFreeOrderAllowed($excludeFreeOrder);
    }

    /**
     * @param bool $excludeFreeOrder
     * @return bool
     */
    protected function isFreeOrderAllowed($excludeFreeOrder) {
        if (!$excludeFreeOrder) return true;

        $order = $this->getOrder();
        $orderTotal = $order->getGrandtotal();
        if ($orderTotal > 0) {
            return true;
        }

        return false;
    }

}
