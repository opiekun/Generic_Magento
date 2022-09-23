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
namespace PluginCompany\ProductPdf\Block\Adminhtml\Config\FontPicker;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use PluginCompany\ProductPdf\Model\FontReader;

class FontSelect
    extends Template
{
    protected $_template = 'PluginCompany_ProductPdf::config/font_select.phtml';

    /**
     * @var \PluginCompany\ProductPdf\Model\FontReaderFactory
     */
    protected $fontReader;

    public function __construct(
        Context $context,
        FontReader $fontReader,
        array $data = []
    ) {
        $this->fontReader = $fontReader;
        parent::__construct(
            $context,
            $data
        );
    }


    public function getFontData()
    {
        return $this->fontReader->getFontImageData();
    }
}