<?php
/**
 *
 * Created by:  Milan Simek
 * Company:     Plugin Company
 *
 * LICENSE: http://plugin.company/docs/magento-extensions/magento-extension-license-agreement
 *
 * YOU WILL ALSO FIND A PDF COPY OF THE LICENSE IN THE DOWNLOADED ZIP FILE
 *
 * FOR QUESTIONS AND SUPPORT
 * PLEASE DON'T HESITATE TO CONTACT US AT:
 *
 * SUPPORT@PLUGIN.COMPANY
 *
 */

namespace PluginCompany\ProductPdf\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Blockposition extends AbstractOption implements ArrayInterface
{

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            '.product-info-main .towishlist' => __('Add to Wish List'),
            '.product-info-main .tocompare' => __('Add to Compare'),
            '.product-info-main .mailto.friend' => __('Email'),
            '.product-info-main .page-title' => __('Page / Product Title'),
            '.product-info-main .product-reviews-summary' => __('Review Summary'),
            '.product-info-main .product-reviews-summary' => __('Review Summary'),
            '.product-info-price' => __('Product Price Section'),
            '.product-info-price .price-box' => __('Product Price'),
            '.product-info-stock-sku' => __('Stock / SKU'),
            '.product-options-wrapper' => __('Product Options'),
            '.product-options-bottom' => __('Product Options Bottom'),
            '.product-options-bottom .field.qty' => __('QTY input box'),
            '#product-addtocart-button, #bundle-slide' => __('Add to Cart button'),
            '.product.attribute.description' => __('Product Description'),
            '.additional-attributes-wrapper' => __('More Information'),
            'custom_selector' => __('Use Custom CSS Selector'),
        ];
    }
}