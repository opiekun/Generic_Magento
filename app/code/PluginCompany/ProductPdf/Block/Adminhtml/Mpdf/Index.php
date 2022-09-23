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
namespace PluginCompany\ProductPdf\Block\Adminhtml\Mpdf;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use PluginCompany\ProductPdf\Setup\MpdfInstaller;

class Index extends Template
{

    protected $_template = "PluginCompany_ProductPdf::mpdf/index.phtml";

    /**
     * Constructor
     *
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function isMpdfInstalled()
    {
        return MpdfInstaller::isMpdfInstalled();
    }
}
