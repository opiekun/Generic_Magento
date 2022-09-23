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
namespace PluginCompany\ProductPdf\Block\Pdf\Content;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Phrase;
use PluginCompany\ProductPdf\Block\Pdf\Content;

class Attributes extends Content
{
    protected $_template = 'PluginCompany_ProductPdf::pdf/content/attributes.phtml';
    private $additionalData = false;

    /**
     * $excludeAttr is optional array of attribute codes to
     * exclude them from additional data array
     *
     * @param array $excludeAttr
     * @return array
     */
    public function getAdditionalData(array $excludeAttr = [])
    {
        if($this->additionalData !== false){
            return $this->additionalData;
        }
        $data = [];
        $product = $this->getProduct();
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
            if(!$attribute->getIsVisibleOnFront()){
                continue;
            }
            if(in_array($attribute->getAttributeCode(), $excludeAttr)){
                continue;
            }
            if(!$this->canShowBooleanNoAttributes() && $this->isBooleanNo($product, $attribute)) {
                continue;
            }
            $value = $attribute->getFrontend()->getValue($product);
            if($value instanceof Phrase) {
                $value = $value->render();
            }
            if(is_string($value)) {
                $value = trim($value);
            }
            if(!$this->canShowEmptyAttributes() && !$value){
                continue;
            }

            if (!$product->hasData($attribute->getAttributeCode())) {
                $value = __('N/A');
            } elseif ((string)$value == '') {
                $value = __('No');
            } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                $value = $this->formatCurrency($value);
            }

            if (is_string($value) && strlen($value)) {
                $data[$attribute->getAttributeCode()] = [
                    'label' => $attribute->getStoreLabel(),
                    'value' => $value,
                    'code'  => $attribute->getAttributeCode()
                ];
            }
        }
        $this->additionalData = $data;
        return $this->additionalData;
    }

    /**
     * @param Product $product
     * @param Attribute $attribute
     * @return bool
     */
    private function isBooleanNo($product, $attribute)
    {
        if($attribute->getFrontendInput() == 'boolean' && $product->getData($attribute->getAttributeCode()) === "0") {
            return true;
        }
        return false;
    }

    protected function _beforeToHtml()
    {
        if(!$this->canRender()){
            $this->setTemplate('');
        }
        return parent::_beforeToHtml();
    }

    private function canRender()
    {
        return (bool)count($this->getAdditionalData());
    }

    public function canShowEmptyAttributes()
    {
        return !$this->getGroupConfigFlag('additional_information/hide_empty_attributes');
    }

    public function canShowBooleanNoAttributes()
    {
        return !$this->getGroupConfigFlag('additional_information/hide_boolean_no_attributes');
    }
}

