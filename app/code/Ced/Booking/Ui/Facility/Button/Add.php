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
namespace Ced\Booking\Ui\Facility\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class Add
 * @package Ced\Booking\Ui\Facility\Button
 */
class Add implements ButtonProviderInterface
{
    /**
     * Add constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Ced\Booking\Helper\Data $bookingHelper
     */
    public function __construct(\Magento\Backend\Block\Widget\Context $context,
                                \Ced\Booking\Helper\Data $bookingHelper)
    {
        $this->_bookingHelper = $bookingHelper;
        $this->urlBuilder = $context->getUrlBuilder();
    }

    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->_bookingHelper->getEnabledBookingTypes()) {
            $data = [
                'label' => __('Add Facility'),
                'class' => 'primary',
                'on_click' => sprintf("location.href = '%s';", $this->getAddFacilityUrl()),
                'sort_order' => 10,
            ];
        } else {
            $data = [
                'label' => __('Enable any booking addon to add facility.'),
                'class' => 'warning warning-message',
                'on_click' => '',
                'sort_order' => 10,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    protected function getAddFacilityUrl()
    {
        return $this->urlBuilder->getUrl('booking/facilities/new');
    }
}
