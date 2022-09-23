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

class ShortDescription extends Content
{
    protected $_template = 'PluginCompany_ProductPdf::pdf/content/short_description.phtml';

    protected function _beforeToHtml()
    {
        if(!$this->canRender()){
            $this->setTemplate('');
        }
        return parent::_beforeToHtml();
    }

    private function canRender()
    {
        return (bool)$this->getProduct()->getShortDescription();
    }

}

