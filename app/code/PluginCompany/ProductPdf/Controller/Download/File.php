<?php
/**
 *
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
 *
 */

namespace PluginCompany\ProductPdf\Controller\Download;

use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;
use PluginCompany\ProductPdf\Adapter\PdfGenerator\Mpdf;
use PluginCompany\ProductPdf\Adapter\PdfGenerator\PdfGeneratorInterface;
use PluginCompany\ProductPdf\Block\Pdf;
use PluginCompany\ProductPdf\Setup\FontDownloader;
use Psr\Log\LoggerInterface;

class File extends Action {

    /**
     * @var ProductFactory
     */
    protected $catalogProductFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var PdfGeneratorInterface
     */
    private $pdfGenerator;

    private $product;

    /** @var Pdf */
    private $pdfBlock;
    /**
     * @var FontDownloader
     */
    private $fontDownloader;

    private $renderedPdfContent;

    public function __construct(
        Context $context,
        ProductFactory $catalogProductFactory,
        Registry $registry,
        LoggerInterface $logger,
        Mpdf $pdfGenerator,
        FontDownloader $fontDownloader
    ) {
        $this->catalogProductFactory = $catalogProductFactory;
        $this->registry = $registry;
        $this->logger = $logger;
        $this->pdfGenerator = $pdfGenerator;
        $this->fontDownloader = $fontDownloader;
        parent::__construct(
            $context
        );
    }

    public function execute()
    {
        if (!$this->getProductId()) {
            return $this->getResponse()->setBody(__("Error downloading file"));
        }
        try {
            return $this->runExecute();
        } catch (\Exception $e){
            $this->logger->log(500, $e->getMessage());
        }
    }

    private function runExecute()
    {
        if(!$this->isMpdfInstalled()){
            return $this->showMpdfNotFoundError();
        }
        if(!$this->isMpdfQrInstalled()){
            return $this->showMpdfQrNotFoundError();
        }
        $this
            ->downloadFontsIfNeeded()
            ->registerProduct()
        ;
        if($this->getRequest()->getParam('html')){
            return $this->printHtml();
        }
        return $this->generatePdf();
    }

    private function isMpdfInstalled()
    {
        return class_exists('Mpdf\Mpdf');
    }

    private function isMpdfQrInstalled()
    {
        return class_exists('Mpdf\QrCode\QrCode');
    }

    private function downloadFontsIfNeeded()
    {
        $this->fontDownloader->installIfNotAvailable();
        return $this;
    }

    private function registerProduct()
    {
        $this->registry->register(
            'current_product',
            $this->getProduct()
        );
        $this->registry->register(
            'product',
            $this->getProduct()
        );
        return $this;
    }

    private function printHtml()
    {
        return $this->getResponse()
            ->setBody(
                $this->getPdfBlock()->toHtml()
            );
    }

    private function getPdfBlock()
    {
        if (!$this->hasPdfBlock()) {
            $this->initPdfBlock();
        }
        return $this->pdfBlock;
    }

    private function hasPdfBlock()
    {
        return isset($this->pdfBlock);
    }

    private function initPdfBlock()
    {
        $this->pdfBlock = $this->getNewPdfBlock();
        return $this;
    }

    private function getNewPdfBlock()
    {
        return $this->_view->getLayout()
            ->createBlock('PluginCompany\ProductPdf\Block\Pdf')
            ->setProduct($this->getProduct());
    }

    private function getProduct()
    {
        if (!$this->hasProduct()) {
            $this->initProduct();
        }
        return $this->product;
    }

    private function hasProduct()
    {
        return isset($this->product);
    }

    private function initProduct()
    {
        $this->product = $this->catalogProductFactory->create()
            ->load($this->getProductId());
        $this->addParentProductToProduct();
        if($this->getParentProductId()) {
            $this->addParentProductToProduct();
        }
        return $this;
    }

    private function addParentProductToProduct()
    {
        $this->product->setParentProduct(
            $this->catalogProductFactory->create()
                ->load(
                    $this->getParentProductId()
                )
        );
        return $this;
    }

    private function getProductId()
    {
        return $this->getRequest()->getParam('id');
    }

    private function getParentProductId()
    {
        return $this->getRequest()->getParam('parent_id');
    }

    private function generatePdf()
    {
        try {
            $this->addFooterToPdf();
            $this->enablePrintWindowIfApplicable();
            $this->streamPdf();
        }
        catch(\Throwable $e) {
            $this->handleError($e);
        }
        catch(\Exception $e) {
            $this->handleError($e);
        }
    }

    private function handleError($e)
    {
        $this->logger->critical($e->getMessage());
        if(stristr($e->getMessage(), 'Temporary files directory')){
            return $this->showTempFileWriteError();
        }
        if(stristr($e->getMessage(), 'Permission denied')){
            return $this->showTempFileWriteError();
        }
        throw $e;
    }

    private function showTempFileWriteError()
    {
        /** @var \Magento\Framework\App\Response\Http $response */
        $response = $this->getResponse();
        return $response
            ->setContent(
                $this->_view
                    ->getLayout()
                    ->createBlock('Magento\Framework\View\Element\Template')
                    ->setTemplate('PluginCompany_ProductPdf::error/mpdf_temp_dir.phtml')
                    ->toHtml()
            )
            ->sendResponse();
    }

    private function showMpdfNotFoundError()
    {
        /** @var \Magento\Framework\App\Response\Http $response */
        $response = $this->getResponse();
        $response
            ->setContent(
                "<p>Please install the mPDF library on your server to view the PDF file. You can use the automatic installer in the admin area to finish the installation.</p>"
            )
            ->sendResponse()
            ;
    }

    private function showMpdfQrNotFoundError()
    {
        /** @var \Magento\Framework\App\Response\Http $response */
        $response = $this->getResponse();
        $response
            ->setContent(
                "<p>Please install the mPDF QR Code library on your server to view the PDF file. You can use the automatic installer in the admin area to finish the installation.</p>"
            )
            ->sendResponse()
        ;
    }

    private function addFooterToPdf()
    {
        if(!$this->getPdfBlock()->canShowFooter()) {
            return $this;
        }
        $this->pdfGenerator
            ->setMarginBottom(15)
            ->setHtmlFooter($this->getPdfBlock()->getFooterHtml())
        ;
        return $this;
    }

    private function enablePrintWindowIfApplicable()
    {
        if($this->getPdfBlock()->printWindowEnabled()) {
            $this->pdfGenerator->setJs('this.print();');
        }
        return $this;
    }

    private function streamPdf()
    {
        /** @var \Magento\Framework\App\Response\Http $response */
        $response = $this->getResponse();
        $response
            ->setContent($this->getRenderedPdfContent())
        ;
        $this->sendPdfHeaders();
        return $response->sendResponse();
    }

    private function sendPdfHeaders()
    {
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', 'application/pdf', true)
            ->setHeader('Content-Disposition', 'inline; filename="' . $this->getFileName() . '"', true)
            ->setHeader('Last-Modified', date('r'), true);
        $this->getResponse()->sendHeaders();
        return $this;
    }

    private function getRenderedPdfContent()
    {
        if(!$this->renderedPdfContent) {
            $this->initRenderedPdfContent();
        }
        return $this->renderedPdfContent;
    }

    private function initRenderedPdfContent()
    {
        $this->renderedPdfContent =
            $this->pdfGenerator
                ->setFileName($this->getFileName())
                ->generate($this->getPdfBlock())
        ;
        return $this;
    }


    private function getFileName()
    {
        return urldecode(str_replace(['____', '___', '__'], '_', preg_replace('/[^\da-z]/i', '_', $this->getProduct()->getName()))).'.pdf';
    }

}
