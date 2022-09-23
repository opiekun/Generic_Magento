<?php
namespace WeltPixel\GoogleTagManager\Block\System\Config\Json;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Export
 * @package WeltPixel\GoogleTagManager\Block\System\Json
 */
class Export extends Field
{
    protected $_template = 'WeltPixel_GoogleTagManager::system/config/json/export_container.phtml';

    /**
     * @var string
     */
    protected $itemJsonGenerationtUrl = null;

    /**
     * @var string
     */
    protected $itemJsonDownloadUrl = null;

    /**
     * Version constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * @return string
     */
    public function getUrlForItemsJsonGeneration()
    {
        $this->itemJsonGenerationtUrl = $this->_urlBuilder->getUrl('googletagmanager/json/generate');
        return $this->itemJsonGenerationtUrl;
    }

    /**
     * @return string
     */
    public function getUrlForItemsJsonDownload()
    {
        $this->itemJsonDownloadUrl = $this->_urlBuilder->getUrl('googletagmanager/json/download');
        return $this->itemJsonDownloadUrl;
    }
}