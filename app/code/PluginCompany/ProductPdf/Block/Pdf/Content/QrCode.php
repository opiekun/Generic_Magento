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

class QrCode extends Content
{

    protected $_template = 'PluginCompany_ProductPdf::pdf/content/qr_code.phtml';

    public function getProductUrl()
    {
        return $this->getProduct()
            ->getProductUrl(false);
    }

    public function getSize()
    {
        return $this->getConfig('size');
    }

    public function getConfigFlag($field)
    {
        return $this->getGroupConfigFlag('qr_code/' . $field);
    }

    public function getConfig($field)
    {
        return $this->getGroupConfig('qr_code/' . $field);
    }

}

