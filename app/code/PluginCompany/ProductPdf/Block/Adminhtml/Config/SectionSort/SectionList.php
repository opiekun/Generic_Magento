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

namespace PluginCompany\ProductPdf\Block\Adminhtml\Config\SectionSort;

use Magento\Framework\View\Element\Template;

class SectionList extends Template
{
    protected $_template = 'PluginCompany_ProductPdf::config/sectionsort/list.phtml';

    public function getValue()
    {
        $value = parent::getValue();
        if(!$value){
            return false;
        }
        return json_decode($value, true);
    }

}