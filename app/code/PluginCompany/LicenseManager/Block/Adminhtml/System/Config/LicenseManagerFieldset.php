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
namespace PluginCompany\LicenseManager\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;
use PluginCompany\LicenseManager\Model\LicenseManager;

class LicenseManagerFieldset extends Fieldset
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Magento\Framework\View\Helper\Js
     */
    protected $_jsHelper;

    /**
     * Whether is collapsed by default
     *
     * @var bool
     */
    protected $isCollapsedDefault = false;

    /**
     * @var LicenseManager
     */
    protected $licenseManager;

    private $element;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\View\Helper\Js $jsHelper
     * @param \PluginCompany\LicenseManager\Model\LicenseManager $licenseManager
     * @param array $data
     */

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\View\Helper\Js $jsHelper,
        LicenseManager $licenseManager,
        array $data = []
    ) {
        $this->_jsHelper = $jsHelper;
        $this->_authSession = $authSession;
        $this->licenseManager = $licenseManager;
        parent::__construct($context, $authSession, $jsHelper, $data);
    }
    /**
     * Render fieldset html
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->setElement($element);

        $header = $this->getHeader() . $this->_getHeaderHtml($element);

        $this->element = $element;
        $this->addLicenseFieldsToElement();

        $elements = $this->_getChildrenElementsHtml($element);

        $footer = $this->_getFooterHtml($element);

        return $header . $elements . $footer;
    }

    private function getHeader()
    {
        $marketplace = false;
        if(!$marketplace){
            return "<p class='message'>Generate your license keys <a target='_blank' href='https://plugin.company/licensemanager/license/index/'>here</a>";
        }else{
            return "
<p class='message'>Thank you for your purchase! 
You will receive instructions by e-mail explaining how to generate your license keys. 
In the meantime you can already start using the purchased extensions without any limitations.
<br><br>If you have any questions or require our assistance, please let us know at <a href='mailto:support@plugin.company'>support@plugin.company</a>
</p>";
        }
    }

    private function addLicenseFieldsToElement()
    {
        foreach($this->getLicenseData() as $name => $license)
        {
            $fieldSet = $this->element->addFieldset(
                $license->getExtensionKey() . '_fieldset',
                ['legend' => __($name), 'class' => 'fieldset-wide fieldset-small']
            );
            $this->addFieldToFieldset($fieldSet, [
                'name' => 'licenseKeys[' . $license->getExtensionKey() . ']',
                'label' => 'License Key',
                'required' => false,
                'disabled' => false,
                'value' => $license->getLicenseKey(),
                'class' => '',
                'comment' => 'Please enter your license key here and click the "Save License" button to validate the license.'
            ]);
            $this->addFieldToFieldset($fieldSet, [
                'name' => $license->getExtensionKey() . '_orderNumber',
                'label' => 'Order #',
                'required' => false,
                'disabled' => true,
                'value' => $license->getOrderId(),
                'class' => 'pcDisabled'
            ]);
            $this->addFieldToFieldset($fieldSet, [
                'name' => $license->getExtensionKey() . '_isValid',
                'label' => 'Is Valid',
                'required' => false,
                'disabled' => true,
                'value' => $license->getIsValid() ? 'Yes' : 'No',
                'class' => 'pcDisabled ' . ($license->getIsValid() ? 'isValid' : 'notValid')
            ]);

            if($license->getLicenseError()){
                $this->addFieldToFieldset($fieldSet, [
                    'name' => $license->getExtensionKey() . '_error',
                    'label' => 'Error',
                    'required' => false,
                    'disabled' => true,
                    'value' => $license->getLicenseError(),
                    'class' => 'pcDisabled'
                ]);
            }

            $this->addFieldToFieldset($fieldSet, [
                'name' => $license->getExtensionKey() . '_isDev',
                'label' => 'License Type',
                'required' => false,
                'disabled' => true,
                'value' => $license->getIsDevelopment() ? 'Development' : 'Live',
                'style' => 'margin-bottom:30px',
                'class' => 'pcDisabled'
            ]);
        }

        return $this;
    }

    private function getLicenseData()
    {
        $data = $this->licenseManager->getProprietaryModuleLicenses();
        return $data;
    }

    private function addFieldToFieldset($fieldSet, $data)
    {
        $style = '';
        if(isset($data['style'])){
            $style = $data['style'];
        }
        $field = $fieldSet
            ->addField(
                $data['name'],
                'text',
                [
                    'name' => $data['name'],
                    'label' => __($data['label']),
                    'id' => $data['name'],
                    'title' => __($data['label']),
                    'required' => $data['required'],
                    'disabled' => $data['disabled'],
                    'value' => $data['value'],
                    'style' => $style,
                    'class' => $data['class'],
                    'comment' => isset($data['comment']) ? $data['comment'] : ''
                ]
            );
        $field->setRenderer($this->getFieldRenderer());
        return $field;
    }

    private function getFieldRenderer()
    {
        return $this->getLayout()->createBlock('Magento\Config\Block\System\Config\Form\Field');
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getChildrenElementsHtml(AbstractElement $element)
    {
        $elements = '';
        foreach ($element->getElements() as $field) {
            if ($field instanceof \Magento\Framework\Data\Form\Element\Fieldset) {
                $elements .= '<tr id="row_' . $field->getHtmlId() . '">'
                    . '<td colspan="4">' . $field->toHtml() . '</td></tr>';
            } else {
                $elements .= $field->toHtml();
            }
        }

        return $elements;
    }

    /**
     * Return js code for fieldset:
     * - observe fieldset rows;
     * - apply collapse;
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getExtraJs($element)
    {
        $htmlId = $element->getHtmlId();
        $output = "require(['prototype'], function(){Fieldset.applyCollapse('{$htmlId}');});";
        $output .= "window.addEventListener('load', function() { require(['jquery'], function($){setTimeout(function(){ $('#save .ui-button-text span').text('Save License')}, 200)}) });";
        return $this->_jsHelper->getScript($output);
    }

}