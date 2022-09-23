<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Booking
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Booking\Block\Adminhtml;

/**
 * Class Config
 * @package Ced\Booking\Block\Adminhtml
 */
class Config extends \Magento\Config\Block\System\Config\Form\Field
{
    public function __construct(\Ced\Booking\Helper\Data $helperData
    ) {

        $this->_helperData = $helperData;
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $apiKey = $this->_helperData->getStoreConfig(\Ced\Booking\Helper\Data::XML_PATH_MAP_API_KEY);
        $html = '';
        $html .= "<script src='https://maps.googleapis.com/maps/api/js?key=".$apiKey."&libraries=places' type='text/javascript'></script>
            <script>
                function gm_authFailure()
                {
                    alert('".__('There has been an error in map API key, please check the map api key.')."');
                }
				</script>";
        return $html;
    }
}
