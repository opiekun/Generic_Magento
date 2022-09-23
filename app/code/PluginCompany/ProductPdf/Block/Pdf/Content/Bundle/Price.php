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
namespace PluginCompany\ProductPdf\Block\Pdf\Content\Bundle;

use PluginCompany\ProductPdf\Block\Pdf\Content;

class Price extends Content
{

    protected $_template = 'PluginCompany_ProductPdf::pdf/content/bundle_price.phtml';

    public function getFormattedMinimalPrice()
    {
        return $this->formatCurrency(
            $this->getMinimalPrice()
        );
    }

    private function getMinimalPrice()
    {
        return $this->getPriceModel()
            ->getMinimalPrice()
            ->getValue();
    }
    public function getFormattedMaximalPrice()
    {
        return $this->formatCurrency(
            $this->getMaximalPrice()
        );
    }

    private function getMaximalPrice()
    {
        return $this->getPriceModel()
            ->getMaximalPrice()
            ->getValue();
    }

    private function getPriceModel()
    {
        return $this->getProduct()
            ->getPriceInfo()
            ->getPrice('final_price');
    }
}

