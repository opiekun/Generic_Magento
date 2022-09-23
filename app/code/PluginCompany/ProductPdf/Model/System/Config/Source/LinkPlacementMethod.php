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
namespace PluginCompany\ProductPdf\Model\System\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class
 */
class LinkPlacementMethod implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('Insert Before'),
                'value' => 'insertBefore'
            ],
            [
                'label' => __('Insert After'),
                'value' => 'insertAfter',
            ],
            [
                'label' => __('Prepend To'),
                'value' => 'prependTo',
            ],
            [
                'label' => __('Append To'),
                'value' => 'appendTo',
            ],
        ];
        return $options;
    }
}

