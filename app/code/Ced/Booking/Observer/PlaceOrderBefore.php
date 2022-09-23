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

namespace Ced\Booking\Observer;

use Ced\Booking\Helper\Data;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class PlaceOrderBefore
 * @package Ced\Booking\Observer
 */
class PlaceOrderBefore implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $_bookingHelper;

    /**
     * PlaceOrderBefore constructor.
     * @param Data $bookingHelper
     */
    public function __construct(
        Data $bookingHelper
    ) {
        $this->_bookingHelper = $bookingHelper;
        $objectManager = ObjectManager::getInstance();
        if ($this->_bookingHelper->isModuleEnabled('Ced_Appointment')) {
            $this->appointmentHelper = $objectManager->create(\Ced\Appointment\Helper\Data::class);
        }
    }

    /**
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        foreach ($order->getAllItems() as $items) {

            /** if the product type is appointment */
            if ($items->getProductType() == Data::APPOINTMENT_PRODUCT_TYPE) {
                $productOptions = $items->getProductOptions();
                if (isset($productOptions['info_buyRequest'])) {
                    $leftQty = $this->appointmentHelper->checkAvailability(false, $productOptions['info_buyRequest']);
                    if ($leftQty < 0) {
                        throw new LocalizedException(__('Not enough quantity.'));
                    }
                }
            }
        }
    }
}
