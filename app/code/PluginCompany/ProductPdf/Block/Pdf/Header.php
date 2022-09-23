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

use Magento\Store\Model\ScopeInterface;
use PluginCompany\ProductPdf\Block\Pdf;

class Header extends Pdf
{
    protected $_template = 'PluginCompany_ProductPdf::pdf/header.phtml';

    public function getStoreLogo()
    {
        if ($this->getConfigFlag('default_img')) {
            return $this->getDefaultStoreLogoUrl();
        }
        if($this->getCustomLogoPath()){
            return $this->getCustomLogoUrl();
        }
        return false;
    }

    public function getDefaultStoreLogoUrl()
    {
        if($this->shouldUseAssetResourcePath()) {
            return $this->getDefaultStoreLogoPath();
        }
        return $this->_layout
            ->createBlock('\Magento\Theme\Block\Html\Header\Logo')
            ->getLogoSrc();
    }

    private function getDefaultStoreLogoPath()
    {
        $folderName = \Magento\Config\Model\Config\Backend\Image\Logo::UPLOAD_DIR;
        $storeLogoPath = $this->_scopeConfig->getValue(
            'design/header/logo_src',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $path = $folderName . '/' . $storeLogoPath;
        if($storeLogoPath !== null && $this->getMediaDirectory()->isFile($path)) {
            return $path;
        }
        return $this->getViewFileUrl('images/logo.svg');
    }

    public function getCustomLogoUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl('media')
            . 'plugincompany/productpdf/'
            . $this->getCustomLogoPath()
            ;
    }

    private function getCustomLogoPath()
    {
        return $this->getConfig('logo_img');
    }

    public function canShowStoreName()
    {
        return $this->getConfigFlag('show_store_title');
    }

    public function getStoreTitleFontFamily()
    {
        if($this->getConfigFlag('use_custom_title_font')){
            return $this->getConfig('store_name_fontfamily');
        }
        return $this->getHeaderFontFamily();
    }

    public function getHeaderFontFamily()
    {
        if($this->getConfigFlag('use_custom_font')){
            return $this->getConfig('fontfamily');
        }
        return $this->getDefaultFont();
    }

    public function getStoreName() {
        if($this->getConfig('store_title')){
            return $this->getConfig('store_title');
        }
        return $this->_scopeConfig->getValue('general/store_information/name', ScopeInterface::SCOPE_STORE);
    }

    public function getConfig($field)
    {
        return parent::getHeaderConfig($field);
    }

    public function getConfigFlag($field){
        return parent::getHeaderConfigFlag($field);
    }

}
