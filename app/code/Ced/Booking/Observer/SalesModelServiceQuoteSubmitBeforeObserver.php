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

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class SalesModelServiceQuoteSubmitBeforeObserver
 * @package Ced\Booking\Observer
 */
class SalesModelServiceQuoteSubmitBeforeObserver implements ObserverInterface
{
       /**
     * @var \Magento\Framework\App\State
     */
    protected $_state;


    /**
     * @var mixed
     */
    protected $_serializer;
    
    public function __construct(\Ced\Booking\Helper\Data $dataHelper,
        \Magento\Framework\App\State $state,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null)
    {
        $this->_dataHelper = $dataHelper;
        $this->_serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
        $this->_state = $state;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $this->quote = $observer->getQuote();
        $this->order = $observer->getOrder();

        foreach($this->order->getItems() as $orderItem)
        {
            if (in_array($orderItem->getProductType(),$this->_dataHelper->getEnabledBookingTypes()) && !$this->_dataHelper->isModuleEnabled('Ced_CsBooking')) {

                if ($this->_state->getAreaCode() !== 'adminhtml') {
                    $this->quote = $observer->getQuote();
                    $this->order = $observer->getOrder();
                    foreach ($this->order->getItems() as $orderItem) {
                        if ($quoteItem = $this->getQuoteItemById($orderItem->getQuoteItemId(), $this->quote)) {
                            $additionalOptions = [];
                            if ($additionalOptionsQuote = $quoteItem->getOptionByCode('additional_options')) {
                                $additionalOptions = class_exists(
                                    "\\Magento\\Framework\\Serialize\\Serializer\\Json")? $this->_serializer->unserialize($additionalOptionsQuote->getValue()) : unserialize($additionalOptionsQuote->getValue());
                            }
                            if (!empty($additionalOptions)) {
                                $options = $orderItem->getProductOptions();
                                $options['additional_options'] = $additionalOptions;
                                $orderItem->setProductOptions($options);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $id
     * @return mixed|null
     */
    private function getQuoteItemById($id, $quote)
    {
        if (empty($this->quoteItems)) {
            /* @var  \Magento\Quote\Model\Quote\Item $item */
            foreach ($quote->getAllItems() as $item) {
                //filter out config/bundle etc product
                $this->quoteItems[$item->getId()] = $item;
            }
        }
        if (array_key_exists($id, $this->quoteItems)) {
            return $this->quoteItems[$id];
        }
        return null;
    }
}