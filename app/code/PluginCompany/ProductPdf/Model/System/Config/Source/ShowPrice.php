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
namespace PluginCompany\ProductPdf\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ShowPrice extends AbstractOption implements ArrayInterface
{

    const USE_DEFAULT = 0;
    const SHOW_PRICE = 1;
    const HIDE_PRICE = 2;

    public function toArray()
    {
        return array(
            0 => 'Use general title & price section settings',
            1 => 'Yes',
            2 => 'No',
        );
    }

}