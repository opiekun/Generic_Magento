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
namespace PluginCompany\ProductPdf\Block\Adminhtml\Product\Edit\Button;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use PluginCompany\ProductPdf\Model\Product\PdfUrl;

class PrintPdf implements ButtonProviderInterface
{
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var PdfUrl
     */
    private $pdfUrlGenerator;

    /**
     * Generic constructor
     *
     * @param Registry $registry
     * @param PdfUrl $pdfUrl
     */
    public function __construct(
        Registry $registry,
        PdfUrl $pdfUrl
    ) {
        $this->registry = $registry;
        $this->pdfUrlGenerator = $pdfUrl;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        if($this->getProduct()->isObjectNew()) {
            return [];
        }
        return [
            'label' => __('Download PDF'),
            'on_click' => sprintf("window.open('%s');", $this->getPdfUrl()),
            'class' => 'action-secondary',
            'sort_order' => 10
        ];
    }

    /**
     * @return string
     */
    public function getPdfUrl()
    {
        return $this->pdfUrlGenerator
            ->setProduct($this->getProduct())
            ->setStoreId($this->getStoreId())
            ->getPdfUrl()
            ;
    }

    /**
     * Get product
     *
     * @return ProductInterface
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    private function getStoreId()
    {
        return $this->getStore()->getStoreId();
    }

    private function getStore()
    {
        /** @var \Magento\Store\Model\Store $store */
        return $this->registry->registry('current_store');
    }


}
