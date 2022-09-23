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

class Footer extends Pdf
{

    protected $_template = 'PluginCompany_ProductPdf::pdf/footer.phtml';

    public function canShowProductUrl()
    {
        return $this->getConfigFlag('show_url');
    }

    public function getProductUrl()
    {
        return $this->getProduct()
            ->getProductUrl(false);
    }

    public function canShowTimeStamp()
    {
        return $this->getConfigFlag('show_timestamp');
    }

    public function getTimeStamp()
    {
        return $this->formatDate();
    }

    public function getConfig($field)
    {
        return parent::getFooterConfig($field);
    }

    public function getConfigFlag($field){
        return parent::getFooterConfigFlag($field);
    }


}