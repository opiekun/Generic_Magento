<?php
namespace ClassWallet\Payment\Controller\Create;

class Order extends \Magento\Framework\App\Action\Action
{

  public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    \Magento\Quote\Model\QuoteManagement $quoteManagement,
    \Magento\Checkout\Model\Session $checkoutSession,
    \Magento\Customer\Model\AddressFactory $addressFactory,
    \Magento\Customer\Model\Session $customerSession,
    \ClassWallet\Payment\Block\Button $buttonBlock
  )
  {
      $this->_storeManager   = $storeManager;
      $this->quoteManagement = $quoteManagement;
      $this->checkoutSession = $checkoutSession;
      $this->addressFactory  = $addressFactory;
      $this->customerSession = $customerSession;
      $this->buttonBlock     = $buttonBlock;
      return parent::__construct($context);
  }

  public function execute()
  {
      try{
            if(!$this->buttonBlock->isButtonEnabled()){
              throw new \Exception(__("Action not allowed"));
            }

            $quoteData        =   $this->checkoutSession->getQuote();
            $shippingAddress  =   $quoteData->getShippingAddress();
            $defaultShipMtd   =   $this->buttonBlock->defaultShippingMethod();

            /* SET SHIPPING ADDRESS */
            
            $shippingAddressId  = $this->customerSession->getCustomer()->getDefaultShipping();
            $shipAddress        = $this->addressFactory->create()->load($shippingAddressId);
            $sAddress           = $shipAddress->getData();

            $quoteData->getShippingAddress()->setFirstname($sAddress['firstname']);
            $quoteData->getShippingAddress()->setLastname($sAddress['lastname']);
            $quoteData->getShippingAddress()->setStreet($sAddress['street']);
            $quoteData->getShippingAddress()->setCity($sAddress['city']);
            $quoteData->getShippingAddress()->setTelephone($sAddress['telephone']);
            $quoteData->getShippingAddress()->setPostcode($sAddress['postcode']);
            $quoteData->getShippingAddress()->setCountryId($sAddress['country_id']);
            $quoteData->getShippingAddress()->setRegion($sAddress['region']);
            $quoteData->getShippingAddress()->setRegionId($sAddress['region_id']);

            $quoteData->getBillingAddress()->setFirstname($sAddress['firstname']);
            $quoteData->getBillingAddress()->setLastname($sAddress['lastname']);
            $quoteData->getBillingAddress()->setStreet($sAddress['street']);
            $quoteData->getBillingAddress()->setCity($sAddress['city']);
            $quoteData->getBillingAddress()->setTelephone($sAddress['telephone']);
            $quoteData->getBillingAddress()->setPostcode($sAddress['postcode']);
            $quoteData->getBillingAddress()->setCountryId($sAddress['country_id']);
            $quoteData->getBillingAddress()->setRegion($sAddress['region']);
            $quoteData->getBillingAddress()->setRegionId($sAddress['region_id']);


            // Collect Rates and Set Shipping & Payment Method
            $shippingAddress->setCollectShippingRates(true)
                            ->collectShippingRates()
                            ->setShippingMethod($defaultShipMtd); //shipping method
            $quoteData->setPaymentMethod('classwallet'); //payment method
            $quoteData->save(); //Now Save quote and your quote is ready

            // Set Sales Order Payment
            $quoteData->getPayment()->importData(['method' => 'classwallet']);
            $quoteData->collectTotals()->save();

            // Create Order From Quote
            $order          =   $this->quoteManagement->submit($quoteData);
            $increment_id   =   $order->getRealOrderId();
            $this->checkoutSession->setLastOrderId($order->getEntityId());
            $this->_redirect('checkout/onepage/success');  
      }catch(\Exception $e){
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_redirect('checkout/cart/index');  
      }
  }
}