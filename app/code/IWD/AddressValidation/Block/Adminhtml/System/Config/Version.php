<?php

namespace IWD\AddressValidation\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Backend\Block\Template\Context;

/**
 * Class Version
 * @package IWD\AddressValidation\Block\Adminhtml\System\Config
 */
class Version extends Field
{
    /**
     * @var ComponentRegistrar
     */
    private $componentRegistrar;

    /**
     * Version constructor.
     * @param Context $context
     * @param ComponentRegistrar $componentRegistrar
     * @param array $data
     */
    public function __construct(Context $context, ComponentRegistrar $componentRegistrar, array $data = [])
    {
        $this->componentRegistrar = $componentRegistrar;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return "<span style='margin-bottom:-8px; display:block;'>" . $this->getVersion() . "</span>";
    }

    /**
     * @return string
     */
    private function getVersion()
    {
        $nameSpace = explode("\\", __NAMESPACE__);
        $moduleName = $nameSpace[0] . '_' . $nameSpace[1];
        $configFile = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, $moduleName)
            . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'module.xml';
        $xml = new \SimpleXMLElement(file_get_contents($configFile));

        return $xml->module[0]->attributes()->setup_version;
    }
}
