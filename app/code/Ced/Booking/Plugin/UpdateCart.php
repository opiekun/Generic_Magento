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
namespace Ced\Booking\Plugin;
use \Magento\Framework\Message\ManagerInterface ;

/**
 * Class UpdateCart
 * @package Ced\Booking\Plugin
 */
class UpdateCart
{

    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * UpdateCart constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        ManagerInterface $messageManager,
        \Ced\Booking\Helper\Data $bookingHelper
    ) {
        $this->quote = $checkoutSession->getQuote();
        $this->_messageManager = $messageManager;
        $this->_bookingHelper = $bookingHelper;
    }

    /**
     * @param \Magento\Checkout\Model\Cart $subject
     * @param $data
     * @return array
     */
    public function beforeupdateItems(\Magento\Checkout\Model\Cart $subject,$data)
    {
        $bookingTypes = $this->_bookingHelper->getAllBookingTypes();
        $quote = $subject->getQuote();
        foreach($data as $key=>$value){
            $item = $quote->getItemById($key);
            if (in_array($item->getProduct()->getTypeId(),$bookingTypes)) {
                $data[$item->getId()]['qty'] = $item->getQty();
                $this->_messageManager->addNoticeMessage('Please go to the Product page to add/modify the number of bookings.');
            }
        }
        return [$data];
    }
}