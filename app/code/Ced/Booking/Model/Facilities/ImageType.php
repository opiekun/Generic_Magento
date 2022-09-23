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
use Ced\Booking\Model\Facilities;
use Ced\Booking\Helper\Data;

/**
 * Class Type
 * @package Ced\Booking\Model\Facilities
 */
class ImageType implements OptionSourceInterface
{

    /**
     * Type constructor.
     * @param Data $bookingHelper
     */
    public function __construct(Data $bookingHelper)
    {
        $this->bookingHelper = $bookingHelper;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [['label' => __('File Upload'), 'value' => Facilities::FACILITY_IMAGE_TYPE],
            ['label' => __('Icon'), 'value' => Facilities::FACILITY_ICON_TYPE]];

        $this->options = $options;

        return $this->options;
    }
}
