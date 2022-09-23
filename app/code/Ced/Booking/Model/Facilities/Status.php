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
use Ced\Booking\Helper\Data;

/**
 * Class Status
 * @package Ced\Booking\Model\Facilities
 */
class Status implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [['value' => Data::STATUS_ENABLED, 'label' => __('Enabled')],
            ['value' => Data::STATUS_DISABLED, 'label' => __('Disabled')]];
        return $options;
    }
}
