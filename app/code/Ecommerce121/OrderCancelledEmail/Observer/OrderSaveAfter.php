<?php

declare(strict_types=1);

namespace Ecommerce121\OrderCancelledEmail\Observer;

use Magento\Framework\App\Area;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\Information as StoreInformation;
use Magento\Store\Model\Store;
use Zonos\Checkout\Setup\Patch\Data\OrderStatusUpdates;

class OrderSaveAfter implements ObserverInterface
{
    const SALES_EMAIL_CANCELLED_ENABLE = 'sales_email/cancelled/enabled';
    const SALES_EMAIL_CANCELLED_IDENTITY = 'sales_email/cancelled/identity';
    const SALES_EMAIL_CANCELLED_TEMPLATE = 'sales_email/cancelled/template';
    const SALES_EMAIL_CANCELLED_GUEST_TEMPLATE = 'sales_email/cancelled/guest_template';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $isSendCancelledMailEnabled = $this->scopeConfig->getValue(self::SALES_EMAIL_CANCELLED_ENABLE);
        if ($isSendCancelledMailEnabled) {
            /**
             * @var Order $order
             */
            $order = $observer->getEvent()->getOrder();
            if ($order->getState() == Order::STATE_CANCELED
                || $order->getState() == OrderStatusUpdates::ZONOS_CANCELED_CODE) {

                try {
                    $selectedSender = $this->scopeConfig->getValue(self::SALES_EMAIL_CANCELLED_IDENTITY);
                    $senderEmail = $this->scopeConfig->getValue('trans_email/ident_'.$selectedSender.'/email');
                    $senderName = $this->scopeConfig->getValue('trans_email/ident_'.$selectedSender.'/name');
                    $recipientEmail = $order->getCustomerEmail();

                    $identifier = ($order->getCustomerIsGuest())
                        ? $this->scopeConfig->getValue(self::SALES_EMAIL_CANCELLED_TEMPLATE)
                        : $this->scopeConfig->getValue(self::SALES_EMAIL_CANCELLED_GUEST_TEMPLATE);

                    $postObject = new DataObject();
                    $postObject->setData([
                        'increment_id' => $order->getIncrementId(),
                        'customer_name' => $order->getCustomerName(),
                        'store_email' => $senderEmail,
                        'store_phone' => $this->scopeConfig->getValue(StoreInformation::XML_PATH_STORE_INFO_PHONE) ?? ''
                    ]);

                    $transport = $this->transportBuilder
                        ->setTemplateIdentifier($identifier)
                        ->setTemplateOptions([
                            'area' => Area::AREA_FRONTEND,
                            'store' => Store::DEFAULT_STORE_ID
                        ])
                        ->setFromByScope([
                            'name' => $senderName,
                            'email' => $senderEmail
                        ])
                        ->setTemplateVars(['data' => $postObject])
                        ->addTo([$recipientEmail])
                        ->getTransport();

                    $transport->sendMessage();

                } catch(\Exception $e) {

                }
            }
        }


    }
}
