<?php
namespace WeltPixel\OwlCarouselSlider\Plugin;

class ConfigView {

    /**
     * @param \Magento\Framework\Config\View $subject
     * @param \Closure $proceed
     * @param string $module
     * @param string $mediaType
     * @param string $mediaId
     * @return array
     */
    public function aroundGetMediaAttributes(
        \Magento\Framework\Config\View $subject,
        \Closure $proceed,
        $module,
        $mediaType,
        $mediaId)
    {
        $result = $proceed($module, $mediaType, $mediaId);
        switch ($mediaId) {
            case "owlcarousel_product_hover" :
            case "related_products_list_hover" :
            case "upsell_products_list_hover" :
            case "cart_cross_sell_products_hover" :
            case "new_products_content_widget_grid_hover" :
                $result['type'] = 'weltpixel_hover_image';
                break;
        }

        return $result;

    }
}