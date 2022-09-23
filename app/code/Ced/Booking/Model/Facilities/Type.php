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
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Booking\Model\Facilities;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 * @package Ced\Booking\Model\Facilities
 */
class Type implements OptionSourceInterface
{
    /**
     * Type constructor.
     * @param \Ced\Booking\Helper\Data $helperData
     */
    public function __construct(\Ced\Booking\Helper\Data $helperData)
    {
        $this->_helperData = $helperData;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $typeArray = $this->_helperData->getEnabledBookingTypes();
        foreach ($typeArray as $type) {
            $options[] = ['label' => __(ucfirst($type)), 'value' => $type];
        }
        $this->options = $options;

        return $this->options;
    }
}
