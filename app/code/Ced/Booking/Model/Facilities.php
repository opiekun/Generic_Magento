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

namespace Ced\Booking\Model;

/**
 * Class Facilities
 * @package Ced\Booking\Model
 */
class Facilities extends \Magento\Framework\Model\AbstractModel
{

    const HOTEL_FACILITY = 'hotel';
    const ROOM_FACILITY = 'room';
    const RENT_FACILITY = 'rent';
    const APPOINTMENT_FACILITY = 'appointment';
    const EVENT_FACILITY = 'event';
    const FACILITY_IMAGE_TYPE = 'image';
    const FACILITY_ICON_TYPE = 'icon';
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry
    )

    {
        $this->_context = $context;
        parent::__construct($context,$registry);

    }

    /**
     * @var construct
     */

    protected function _construct()
    {
        $this->_init('Ced\Booking\Model\ResourceModel\Facilities');
    }


}