<?php

declare(strict_types=1);

namespace Ecommerce121\ChargeLogicConnect\Model;

class Payment extends \ChargeLogic\Connect\Model\Payment
{
    /**
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param $amount
     * @return $this|Payment
     * @throws \Exception
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();

        /** @var \Magento\Sales\Model\Order\Address $billing */
        $billing_address = $order->getBillingAddress() ?? false;
        $shipping_address = $order->getShippingAddress() ?? false;

        // couldn't avoid this, ChargeLogic_Connect module's a mess, sorry.
        require_once('ChargeLogicConnect.php');
        $connect = new \ConnectClient();
        $creds = new \ConnectCredential();
        $card = new \ConnectCard();
        $trans = new \ConnectTransaction();
        $billing = new \ConnectAddress();
        $shipping = new \ConnectAddress();
        $hostedpayment = new \ConnectHostedPayment();
        $response = new \ConnectResponse();


        if ($this->getConfigData('test_mode') == "1") {
            $creds->StoreNo = $this->getConfigData('store_no_test');
            $creds->APIKey = $this->decrypt($this->getConfigData('api_key_test'));

            $creds->ApplicationNo = "WMCATA";
            $creds->ApplicationVersion = "2.0.4";
        } else {
            $creds->StoreNo = $this->getConfigData('store_no');
            $creds->APIKey = $this->decrypt($this->getConfigData('api_key'));

            $creds->ApplicationNo = "WMCATA";
            $creds->ApplicationVersion = "2.0.4";
        }

        $trans->Amount = $amount;
        $trans->Currency = $order->getBaseCurrencyCode();
        $trans->FreightAmount = $order->getShippingAmount();
        $trans->ExternalReferenceNumber = $order->getIncrementId();

        $lineCount = 0;
        $lineItems = array();
        $items = $order->getAllVisibleItems();
        $withOutShipping = false;
        foreach ($items as $item)
        {
            array_push($lineItems, new \ConnectLineItem());
            $lineItems[$lineCount]->Description = substr($item->getName(), 0, 50);
            $lineItems[$lineCount]->Category = "";
            $lineItems[$lineCount]->UnitPrice = $item->getPrice();
            $lineItems[$lineCount]->ProductCode = substr($item->getSku(), 0, 20);
            $item->getProductId();
            $lineItems[$lineCount]->Quantity = $item->getQtyOrdered();
            $lineItems[$lineCount]->UnitOfMeasure = "PCE";
            $lineItems[$lineCount]->LineAmount = ($item->getPrice() * $item->getQtyOrdered()) - $item->getDiscountAmount();
            $lineItems[$lineCount]->LineDiscountAmount = $item->getDiscountAmount();

            $lineCount++;

            if (
                ($item->getTypeId() == 'downloadable' || $item->getTypeId() == 'virtual')
                &&
                !$withOutShipping
            ) {
                $withOutShipping = true;
            }
        }

        $trans->addLineItemArray($lineItems);

        if ($billing_address) {
            $billing->Name = substr($billing_address->getName(), 0, 50);
            $billing->StreetAddress = substr($billing_address->getStreetLine(1), 0, 50);
            $billing->StreetAddress2 = substr($billing_address->getStreetLine(2), 0, 50);
            $billing->City = substr($billing_address->getCity(), 0, 30);
            $billing->State = substr($billing_address->getRegion(), 0, 30);
            $billing->PostCode = substr($billing_address->getPostcode(), 0, 30);

            $billing_country_iso = $this->getCountryInfo($billing_address->getCountryId());
            $billing->Country = $billing_country_iso[2];
            $billing->PhoneNumber = substr($billing_address->getTelephone(), 0, 30);
            $billing->Email = substr($order->getCustomerEmail(), 0, 80);
        }

        if ($shipping_address) {
            $shipping->Name = substr($shipping_address->getName(), 0, 50);
            $shipping->StreetAddress = substr($shipping_address->getStreetLine(1), 0, 50);
            $shipping->StreetAddress2 = substr($shipping_address->getStreetLine(2), 0, 50);
            $shipping->City = substr($shipping_address->getCity(), 0, 30);
            $shipping->State = substr($shipping_address->getRegion(), 0, 30);
            $shipping->PostCode = substr($shipping_address->getPostcode(), 0, 30);

            $shipping_country_iso = $this->getCountryInfo($shipping_address->getCountryId());
            $shipping->Country = $shipping_country_iso[2];
            $shipping->PhoneNumber = substr($shipping_address->getTelephone(), 0, 30);
            $shipping->Email = substr($order->getCustomerEmail(), 0, 80);
        } else if ($withOutShipping) {
            $shipping->Name = substr($billing_address->getName(), 0, 50);
            $shipping->StreetAddress = substr($billing_address->getStreetLine(1), 0, 50);
            $shipping->StreetAddress2 = substr($billing_address->getStreetLine(2), 0, 50);
            $shipping->City = substr($billing_address->getCity(), 0, 30);
            $shipping->State = substr($billing_address->getRegion(), 0, 30);
            $shipping->PostCode = substr($billing_address->getPostcode(), 0, 30);

            $shipping_country_iso = $this->getCountryInfo($billing_address->getCountryId());
            $shipping->Country = $shipping_country_iso[2];
            $shipping->PhoneNumber = substr($billing_address->getTelephone(), 0, 30);
            $shipping->Email = substr($order->getCustomerEmail(), 0, 80);
        }

        if ($shipping_address) {
            $shippingmethod = explode("_", $order->getShippingMethod());

            if (count($shippingmethod) > 0) {
                $hostedpayment->ShippingAgent = substr($shippingmethod[0], 0, 10);
            }

            if (count($shippingmethod) > 1) {
                $hostedpayment->ShippingAgentService = substr($shippingmethod[1], 0, 10);
            }
        } else {
            $hostedpayment->ShippingAgentService = '';
        }

        $hostedpayment->RequireCVV = "No";
        $hostedpayment->Language = "ENG";
        $hostedpayment->ConfirmationID = $trans->ConfirmationID;

        $card->AccountNumber = $payment->getCcNumber();
        $card->ExpirationMonth = sprintf('%02d',$payment->getCcExpMonth());
        $card->ExpirationYear = substr($payment->getCcExpYear(),-2,2);
        $card->CardVerificationValue = $payment->getCcCid();


        try {
            $return_value = $connect->CreditCardAuthorize($creds, $card, $trans, $billing, $response);

            if ($response->TransactionStatus == "Approved") {
                if($this->getConfigData('send_order_info') == "1") {
                    $connect->FinalizeOrder($creds, $connect->SetupHostedOrder($creds, $trans, $billing, $shipping, $hostedpayment));
                }

                $payment
                    ->setTransactionId($return_value)
                    ->setIsTransactionClosed(0);

                return $this;
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(__('Payment capturing error.'));
            }
        }
        catch(Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Payment capturing error.'));
        }
    }
}
