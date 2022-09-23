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
namespace PluginCompany\ProductPdf\Block\Adminhtml\Config;

use Magento\Framework\View\Element\Template;

class CodeFlaskCss extends Template
{

    protected $_template = 'PluginCompany_ProductPdf::config/codeflask_css.phtml';

    public function isProductPdfConfigPage()
    {
        return $this->getRequest()->getParam('section') == 'plugincompany_productpdf';
    }

    protected function _toHtml()
    {
        if(!$this->isProductPdfConfigPage()) {
            return '';
        }
        return parent::_toHtml();
    }

    public function getCodeFlaskCssUrl()
    {
        return $this->_assetRepo->createAsset(
            'PluginCompany_ProductPdf::css/codeflask.css')
            ->getUrl();
    }

}