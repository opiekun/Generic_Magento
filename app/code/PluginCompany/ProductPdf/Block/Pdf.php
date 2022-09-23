<?php
/**
 * Created by:  Milan Simek
 * Company:     Plugin Company
 *
 * LICENSE: http://plugin.company/docs/magento-extensions/magento-extension-license-agreement *
 * YOU WILL ALSO FIND A PDF COPY OF THE LICENSE IN THE DOWNLOADED ZIP FILE
 *
 * FOR QUESTIONS AND SUPPORT
 * PLEASE DON'T HESITATE TO CONTACT US AT:
 *
 * SUPPORT@PLUGIN.COMPANY
 */
namespace PluginCompany\ProductPdf\Block;

use Magento\Catalog\Model\Product\Gallery\ReadHandler;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Data\CollectionFactory;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Widget\Model\Template\FilterFactory;
use PluginCompany\ProductPdf\Helper\ImageCompare;

class Pdf extends Template
{
    private $product;

    /** @var  Filter */
    private $filter;

    protected $_template = 'PluginCompany_ProductPdf::pdf.phtml';

    private $templateFilterFactory;

    /**
     * @var DirectoryList
     */
    private $directoryList;
    private $dataCollectionFactory;
    private $imageCompare;
    private $pricingHelper;
    /**
     * @var
     */
    private $galleryReadHandler;

    public function __construct(
        Context $context,
        FilterFactory $templateFilterFactory,
        DirectoryList $directoryList,
        CollectionFactory $collectionFactory,
        ImageCompare $imageCompare,
        Data $pricingHelper,
        ReadHandler $galleryReadHandler,
        $data = []
    ) {
        $this->templateFilterFactory = $templateFilterFactory; $this->directoryList = $directoryList;
        $this->dataCollectionFactory = $collectionFactory;
        $this->imageCompare = $imageCompare;
        $this->pricingHelper = $pricingHelper;
        $this->galleryReadHandler = $galleryReadHandler;
        $this->addData(array('cache_lifetime' => null));
        return parent::__construct($context, $data);
    }

    public function canShowHeader()
    {
        return $this->getHeaderConfig('show_header');
    }

    public function getHeaderHtml()
    {
        return $this->getLayout()
            ->createBlock('PluginCompany\ProductPdf\Block\Pdf\Header')
            ->setProduct($this->getProduct())
            ->toHtml();
    }

    public function getContentHtml()
    {
        return $this->getLayout()
            ->createBlock('PluginCompany\ProductPdf\Block\Pdf\Content')
            ->setProduct($this->getProduct())
            ->toHtml();
    }

    public function canShowFooter()
    {
        return $this->getFooterConfig('show_footer');
    }

    public function getFooterHtml()
    {
        return $this->getLayout()
            ->createBlock('PluginCompany\ProductPdf\Block\Pdf\Footer')
            ->setProduct($this->getProduct())
            ->toHtml();
    }

    public function getConfig($field)
    {
        return $this->getGroupConfig('general/' . $field);
    }

    public function getHeaderConfig($field)
    {
        return $this->getGroupConfig('header/' . $field);
    }

    public function getHeaderConfigFlag($field){
        return $this->getGroupConfigFlag('header/' . $field);
    }

    public function getSectionHeaderConfigFlag($field){
        return $this->getGroupConfigFlag('section_header/' . $field);
    }

    public function getSectionHeaderConfig($field){
        return $this->getGroupConfig('section_header/' . $field);
    }

    public function getGalleryConfigFlag($field){
        return $this->getGroupConfigFlag('gallery/' . $field);
    }

    public function getGalleryConfig($field){
        return $this->getGroupConfig('gallery/' . $field);
    }

    public function getFooterConfigFlag($field){
        return $this->getGroupConfigFlag('footer/' . $field);
    }

    public function getFooterConfig($field){
        return $this->getGroupConfig('footer/' . $field);
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    public function isProductConfigurable()
    {
        return $this->getProduct()->getTypeId() == 'configurable';
    }

    public function getParentProduct()
    {
        return $this->getProduct()->getParentProduct();
    }

    public function productHasParent()
    {
        return $this->getProduct()->getParentProduct() !== null;
    }

    public function getConfigurableProductChildren()
    {
        return $this->getProduct()
            ->getTypeInstance()
            ->getUsedProductCollection($this->getProduct())
            ;
    }

    public function isProductBundle()
    {
        return $this->getProduct()->getTypeId() == 'bundle';
    }

    public function isProductGrouped()
    {
        return $this->getProduct()->getTypeId() == 'grouped';
    }

    public function canShowMainPrice()
    {
        if($this->isProductGrouped()){
            return false;
        }
        return $this->canShowPrice();
    }

    public function canShowPrice()
    {
        return $this->getGroupConfigFlag('title_price/show_product_price');
    }

    public function getDefaultFont()
    {
        return $this->getGroupConfig('general/default_fontfamily');
    }

    public function getPageNumberFontFamily()
    {
        if($this->getFooterConfigFlag('use_custom_pagenumber_font')){
            return $this->getFooterConfig('pagenumber_font_family');
        }
        return $this->getFooterFontFamily();
    }

    public function getFooterFontFamily()
    {
        if($this->getFooterConfigFlag('use_custom_footer_font')){
            return $this->getFooterConfig('footer_font_family');
        }
        return $this->getDefaultFont();
    }

    public function getGroupConfig($config)
    {
        return $this->_scopeConfig->getValue(
            'plugincompany_productpdf/' . $config, ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()->getId()
        );
    }

    public function getGroupConfigFlag($configFlag)
    {
        return $this->_scopeConfig->isSetFlag(
            'plugincompany_productpdf/' . $configFlag, ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()->getId()
        );
    }

    protected function _toHtml()
    {
        return $this->getFilter()
            ->filter(parent::_toHtml());
    }

    public function getFilter()
    {
        if(!$this->filter){
            $this->initFilter();
        }
        return $this->filter;
    }

    private function initFilter()
    {
        $this->filter = $this->templateFilterFactory->create();
        return $this;
    }

    public function formatCurrency($value)
    {
        return $this->pricingHelper
            ->currency($value, true, false);
    }

    /**
     * @return DirectoryList
     */
    public function getDirectoryList()
    {
        return $this->directoryList;
    }

    /**
     * @return CollectionFactory
     */
    public function getDataCollectionFactory()
    {
        return $this->dataCollectionFactory;
    }

    /**
     * @return ImageCompare
     */
    public function getImageCompare()
    {
        return $this->imageCompare;
    }

    public function getFullProductImageUrl($path)
    {
        $path = $this->addDsToPath($path);
        return $this->_storeManager->getStore()->getBaseUrl('media')
            . 'catalog/product'
            . $path
            ;
    }

    public function addDsToPath($path)
    {
        if(substr($path, 0, 1) != DIRECTORY_SEPARATOR) {
            $path = DIRECTORY_SEPARATOR . $path;
        }
        return $path;
    }

    /**
     * @return ReadHandler
     */
    public function getGalleryReadHandler()
    {
        return $this->galleryReadHandler;
    }

    protected function _afterToHtml($html)
    {
        if(!$this->shouldUseAssetResourcePath()) {
            return parent::_afterToHtml($html);
        }
        $html = str_replace(
            [
                $this->getStaticUrl(false),
                $this->getStaticUrl(true),
                $this->getMediaUrl(false),
                $this->getMediaUrl(true)
            ],
            [
                $this->getStaticPath(),
                $this->getStaticPath(),
                $this->getMediaPath(),
                $this->getMediaPath()
            ],
            $html
        );
        return parent::_afterToHtml($html);
    }

    private function getMediaUrl($secure = false)
    {
        return $this->_storeManager->getStore()->getBaseUrl('media', $secure);
    }

    private function getMediaPath()
    {
        return $this->directoryList->getPath('media') . '/';
    }

    private function getStaticUrl($secure = false)
    {
        return $this->_storeManager->getStore()->getBaseUrl('static', $secure);
    }

    private function getStaticPath()
    {
        return $this->directoryList->getPath('static') . '/';
    }

    public function getViewFileUrl($fileId, array $params = [])
    {
        if($this->shouldUseAssetResourcePath()) {
            return $this->_assetRepo->createAsset($fileId, $params)->getSourceFile();
        }
        return parent::getViewFileUrl($fileId, $params);
    }

    public function shouldUseAssetResourcePath()
    {
        return $this->getGroupConfigFlag('general/use_resource_path') && !$this->getRequest()->getParam('html');
    }

    public function printWindowEnabled()
    {
        return $this->getGroupConfigFlag('frontend/enable_print_window');
    }

}