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

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class SectionSort
    extends Field
{
    /**
     * @var AbstractElement
     */
    private $element;

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $this->element = $element;
        $element->setType('hidden');
        return $this->generateHtml();
    }


    private function getFontSelectHtml()
    {

        return $this->getLayout()
            ->createBlock('PluginCompany\ProductPdf\Block\Adminhtml\Config\SectionSort\SectionList')
            ->setElementHtmlId($this->element->getHtmlId())
            ->setHtmlId($this->element->getHtmlId() . '-sort')
            ->setValue($this->element->getValue())
            ->toHtml();
    }

    private function generateHtml()
    {
        return
            $this->element->getElementHtml()
            . $this->getFontSelectHtml();

        return $this;
    }

}