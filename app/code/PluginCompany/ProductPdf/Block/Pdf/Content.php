<?php
/**
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
 */
namespace PluginCompany\ProductPdf\Block\Pdf;

use PluginCompany\ProductPdf\Block\Pdf;

class Content extends Pdf
{

    protected $_template = 'PluginCompany_ProductPdf::pdf/content.phtml';

    public function getSortedContentElementsKeys()
    {
        return array_keys($this->getSortedContentElements());
    }

    public function getSortedContentElements()
    {
        return json_decode($this->getSectionSortConfig(), true);
    }

    private function getSectionSortConfig()
    {
        return $this->_scopeConfig->getValue(
                'plugincompany_productpdf/sectionsort/sort_order', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $this->_storeManager->getStore()->getId()
            );
    }

    public function getContentElementHtml($key)
    {
        switch ($key) {
            case 'title-price':
                return $this->getTitleAndPriceHtml();
                break;
            case 'image-gallery':
                return $this->createChildBlockIfAllowed(
                    'gallery/show_images',
                    'Content\Media'
                );
                break;
            case 'short-description':
                return $this->createChildBlockIfAllowed(
                    'short_description/show_short_description',
                    'Content\ShortDescription'
                );
                break;
            case 'description':
                return $this->createChildBlockIfAllowed(
                    'description/show_description',
                    'Content\Description'
                );
                break;
            case 'product-attributes':
                return $this->createChildBlockIfAllowed(
                    'additional_information/show_additional_information',
                    'Content\Attributes'
                );
                break;
            case 'configurable-options':
                return $this->getConfigurableOptionsHtmlIfAllowed();
                break;
            case 'bundle-options':
                return $this->getBundleOptionsHtmlIfAllowed();
                break;
            case 'grouped-options':
                return $this->getGroupedOptionsHtmlIfAllowed();
                break;
            case 'qr-code':
                return $this->createChildBlockIfAllowed(
                    'qr_code/show_qr_code',
                    'Content\QrCode'
                );
            case 'custom-options':
                return $this->createChildBlockIfAllowed(
                    'custom_options/show_custom_options',
                    'Content\CustomOptions'
                );
                break;
        }
    }

    public function getTitleAndPriceHtml()
    {
        return $this->getBlockHtmlWithProduct(
            'PluginCompany\ProductPdf\Block\Pdf\Content\TitleAndPrice'
        );
    }

    private function createChildBlockIfAllowed($configFlag, $block)
    {
        if($this->getGroupConfigFlag($configFlag)){
            return $this->getBlockHtmlWithProduct(
                'PluginCompany\ProductPdf\Block\Pdf\\' . $block
            );
        }
        return '';
    }

    public function getConfigurableOptionsHtmlIfAllowed()
    {
        if($this->canShowConfigurableOptions()){
            return $this->getConfigurableOptionsHtml();
        }
        return '';
    }

    public function canShowConfigurableOptions()
    {
        return $this->isProductConfigurable() && $this->getGroupConfigFlag('configurable_product/show_configurable_product_options');
    }

    public function getConfigurableOptionsHtml()
    {
        return $this->getBlockHtmlWithProduct(
            'PluginCompany\ProductPdf\Block\Pdf\Content\ConfigurableOptions'
        );
    }

    public function getBundleOptionsHtmlIfAllowed()
    {
        if($this->canShowBundleOptions()){
            return $this->getBundleOptionsHtml();
        }
        return '';
    }

    public function canShowBundleOptions()
    {
        return $this->isProductBundle() && $this->getGroupConfigFlag('bundle_product/show_bundle_product_options');
    }

    public function getBundleOptionsHtml()
    {
        return $this->getBlockHtmlWithProduct(
            'PluginCompany\ProductPdf\Block\Pdf\Content\BundleOptions'
        );
    }

    public function getGroupedOptionsHtmlIfAllowed()
    {
        if($this->canShowGroupedOptions()){
            return $this->getGroupedOptionsHtml();
        }
        return '';
    }

    public function canShowGroupedOptions()
    {
        return $this->isProductGrouped() && $this->getGroupConfigFlag('grouped_product/show_grouped_product_options');
    }

    public function getGroupedOptionsHtml()
    {
        return $this->getBlockHtmlWithProduct(
            'PluginCompany\ProductPdf\Block\Pdf\Content\GroupedOptions'
        );
    }

    protected function getBlockHtmlWithProduct($blockClass)
    {
        return $this->getLayout()
            ->createBlock($blockClass)
            ->setProduct($this->getProduct())
            ->toHtml();
    }

    public function getSectionTitleFontFamily()
    {
        if($this->getSectionHeaderConfigFlag('use_custom_section_header_font')){
            return $this->getSectionHeaderConfig('section_header_fontfamily');
        }
        return $this->getDefaultFont();
    }

    public function getRowClass($i)
    {
        if($i % 2 == 0){
            return 'odd';
        }
        return 'even';
    }



}