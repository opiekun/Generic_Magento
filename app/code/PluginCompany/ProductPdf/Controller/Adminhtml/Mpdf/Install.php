<?php

namespace PluginCompany\ProductPdf\Controller\Adminhtml\Mpdf;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http;
use Magento\Framework\View\Result\PageFactory;
use PluginCompany\ProductPdf\Controller\Adminhtml\Mpdf;
use PluginCompany\ProductPdf\Setup\MpdfInstaller;

class Install extends Mpdf
{
    /**
     * @var MpdfInstaller
     */
    private $installer;

    /**
     * @param Context $context
     * @param MpdfInstaller $installer
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        MpdfInstaller $installer
    ) {
        $this->installer = $installer;
        parent::__construct($context, $resultPageFactory);
    }


    public function execute()
    {
        if($this->isMpdfInstalled()){
            return $this->sendSuccessResponse();
        }
        return $this->runInstall();
    }

    private function sendSuccessResponse()
    {
        /** @var Http $response */
        $response = $this->getResponse();
        return $this->getResponse()->setBody('success');
    }

    private function runInstall()
    {
        try{
            $this->installMPDF();
        }
        catch(\Exception $e) {}
        catch(\Throwable $e) {};

//        return $this->_redirect('*/*/index', ['tried_automatic_install' => true]);
        return $this->sendSuccessResponse();
    }

    private function installMPDF()
    {
        $this->installer
            ->runInstall();
        return $this;
    }

    private function isMpdfInstalled()
    {
        return MpdfInstaller::isMpdfInstalled();
    }

}
