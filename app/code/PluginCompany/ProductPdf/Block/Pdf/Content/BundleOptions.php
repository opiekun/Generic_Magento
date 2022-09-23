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
namespace PluginCompany\ProductPdf\Block\Pdf\Content;

use PluginCompany\ProductPdf\Block\Pdf\Content;
use PluginCompany\ProductPdf\Model\System\Config\Source\ShowPrice;

class BundleOptions extends Content
{
    protected $_template = 'PluginCompany_ProductPdf::pdf/content/options/bundle.phtml';

    private $bundleOptions;

    /**
     * Return an array of bundle product options
     *
     * @return array
     */
    public function getOptions()
    {
        if(!$this->bundleOptions) {
            try {
                $this->initBundleOptions();
            }
            catch(\Exception $e) {
                $this->_logger->critical($e->getMessage());
            }
        }
        return $this->bundleOptions;
    }

    private function initBundleOptions()
    {
        $this->bundleOptions =
            $this->getLayout()
                ->createBlock('Magento\Bundle\Block\Catalog\Product\View\Type\Bundle')
                ->setProduct($this->getProduct())
                ->getOptions();
        return $this;
    }

    public function getFullImageUrl($path)
    {
        return $this->getFullProductImageUrl($path);
    }

    public function canShowPrice()
    {
        if($this->getShowPriceSetting() == ShowPrice::SHOW_PRICE) {
            return true;
        }
        if($this->getShowPriceSetting() == ShowPrice::HIDE_PRICE) {
            return false;
        }
        return parent::canShowPrice();
    }

    private function getShowPriceSetting()
    {
        return (int)$this->getGroupConfig('bundle_product/show_price');
    }

    public function getFormattedItemPrice($item)
    {
        return $this->formatCurrency(
            $this->getItemPrice($item)
        );
    }

    private function getItemPrice($item)
    {
        return $item->getPrice();
    }

    protected function _toHtml()
    {
        if(!$this->getOptions()) {
            return '';
        }
        return parent::_toHtml();
    }

}

