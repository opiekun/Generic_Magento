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
namespace Ced\Booking\Block\Adminhtml\Dashboard;

use Magento\Backend\Block\Template;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

class Calendar extends Template
{
    /**
     * @var Timezone
     */
    protected $_timezone;

    /**
     *
     * @var PriceHelper
     */
    protected $_priceHelper;

    function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        Timezone $timezone,
        PriceHelper $priceHelper,
        array $data = []
    )
    {
        $this->_timezone = $timezone;
        $this->_priceHelper = $priceHelper;
        parent::__construct($context, $data);
    }

}