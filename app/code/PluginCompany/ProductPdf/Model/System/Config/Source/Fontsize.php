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

class Fontsize extends AbstractOption implements ArrayInterface
{

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            '8px' => '8px',
            '9px' => '9px',
            '10px' => '10px',
            '11px' => '11px',
            '12px' => '12px',
            '13px' => '13px',
            '14px' => '14px',
            '15px' => '15px',
            '16px' => '16px',
            '18px' => '18px',
            '20px' => '20px',
            '22px' => '22px',
            '24px' => '24px',
            '25px' => '25px',
            '26px' => '26px',
            '28px' => '28px',
            '30px' => '30px',
            '35px' => '35px',
            '40px' => '40px',
            '45px' => '45px',
            '50px' => '50px',
       );
    }
}