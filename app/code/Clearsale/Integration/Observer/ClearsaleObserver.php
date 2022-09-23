<?php
namespace Clearsale\Integration\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use \Clearsale\Integration\Model\Order\Business\ObjectObject;
use \Clearsale\Integration\Model\Order\Entity\Status;

class ClearsaleObserver implements ObserverInterface
{
    protected $caseCreationService;
    protected $logger;
    protected $clearSaleTotalConfig;
    protected $orderRepository;
    protected $objectObject;
    protected $sessionManager;
    protected $auth;
    protected $customerRepository;
    protected $countryFactory;
    protected $orderFactory;
    protected $personFactory;
    protected $phoneFactory;
    protected $itemFactory;
    protected $searchCriteriaBuilder;
    protected $filterBuilder;
    protected $filterGroupBuilder;
    protected $invoiceService;
    protected $transaction;
    protected $storeManager;

    public function __construct(LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Clearsale\Integration\Model\Order\Business\ObjectObject $objectObject,
        \Clearsale\Integration\Model\Auth\Business\AuthBusinessObject $auth,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Clearsale\Integration\Model\Order\Entity\OrderFactory $orderFactory,
        \Clearsale\Integration\Model\Order\Entity\PersonFactory $personFactory,
        \Clearsale\Integration\Model\Order\Entity\PhoneFactory $phoneFactory,
        \Clearsale\Integration\Model\Order\Entity\ItemFactory $itemFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->logger = $logger;
        $this->auth = $auth;
        $this->clearSaleTotalConfig = $scopeConfig;
        $this->orderRepository = $orderRepository;
        $this->objectObject = $objectObject;
        $this->sessionManager = $sessionManager;
        $this->customerRepository = $customerRepository;
        $this->countryFactory = $countryFactory;
        $this->orderFactory = $orderFactory;
        $this->phoneFactory = $phoneFactory;
        $this->personFactory = $personFactory;
        $this->itemFactory = $itemFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->storeManager = $storeManager;
    }

    private function extractOrders(Event $event)
    {
        $order = $event->getData('order');
        if (null !== $order) {
            return [$order];
        }

        return $event->getData('orders');
    }

    private function getStoreIDs()
    {
        $storeManagerDataList = $this->storeManager->getStores();
        $ids = array();

        $i = 0;

        foreach ($storeManagerDataList as $key => $value) {
            $ids[$i] = $key;
            $i++;
        }

        return $ids;
    }

    public function orderExists($order)
    {
        $return = true;
        try {
            if ($order) {
                $storeId = $order->getStoreId();

                $analyzing_clearsale = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/analyzing_clearsale', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
                $approved_clearsale = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/approved_clearsale', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
                $denied_clearsale = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/denied_clearsale', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
                $canceled_clearsale = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/canceled_clearsale', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);

                $csStatus = array($approved_clearsale, $denied_clearsale, $canceled_clearsale, $analyzing_clearsale, 'holded', 'complete', 'canceled', 'closed');
                $payment = $order->getPayment();
                $history = $order->getStatusHistoryCollection();

                if ($history) {
                    foreach ($history as $comment) {
                        $this->logger->info('HistoryStatus -> ' . $comment->getStatus());
                        if (in_array($comment->getStatus(), $csStatus)) {
                            return true;
                        }
                    }
                }
                return false;
            }
            return $return;
        } catch (\Exception $e) {
            $this->logger->info('OrderExistsFunction: ' . $e->getMessage());
            return false;
        }
    }

    public function execute(Observer $observer, $ignoreDPM = true)
    {
        try {
            $orders = $this->extractOrders(
                $observer->getEvent()
            );

            if (null === $orders) {
                return;
            }

            $order = $orders[0];
            $orderID = $order->getId();
            $storeId = $order->getStoreId();

            $isActive = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);

            if ($isActive) {
                
                if ($order->dataHasChangedFor('state') && $order->getStatus() != 'pending_payment') {

                    $payment = $order->getPayment();

                    if ($payment) {
                        if ($ignoreDPM && $payment->getMethod() == 'authorizenet_directpost' &&
                            $payment->getMethodInstance()->getResponse()->getXResponseCode() != 1) {
                            $this->logger->info('Clearsale:Will not send to Clearsale - DPM - not approved by Authorize.net- OrderID: ' . $order->getRealOrderId());
                            return;
                        }

                        if (!$this->orderExists($order)) {
                            $creditcardMethods = explode(",", $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/credicardmethod', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId));

                            $magentoStatus = $order->getStatus();

                            if (in_array($payment->getMethodInstance()->getCode(), $creditcardMethods)) {
                                //$orderStatus = new Status();
                                $session = $this->sessionManager->getSessionId();
                                $payment->setAdditionalInformation('clearsaleSessionID', $session);
                                // $orderStatus->order = $order;
                                // $orderStatus->payment = $payment;
                                // $orderStatus->Status = 'PED';

                                //$this->objectObject->saveWithoutStore($orderStatus);

                                $payment->setAdditionalInformation('clearsale', 'ok');
                                // $this->logger->info('Ready to send to ClearSale OrderID - ' . $orderID);
                                // $this->logger->info('Status: ' . $magentoStatus . ' ID: ' . $orderID);

                            }

                        } else {
                            $this->logger->info('Order Update : ID: ' . $orderID);
                        }
                    }

                }

            }
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }

    public function sendPendingOrders()
    {
        $this->logger->info('Method sendPendingOrders called...');

        $storeIds = $this->getStoreIDs();
        $filters = [];
        $i = 0;

        foreach ($storeIds as $storeId) {
            $pendingClearSaleStatuses = explode(",", $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/pending_clearsale', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId));

            $this->logger->info('StoreId: ' . $storeId . ' - Pending ClearSale Statuses: ' . json_encode($pendingClearSaleStatuses));

            $numberPendingClearSaleStatuses = count($pendingClearSaleStatuses);

            for ($j = 0; $j < $numberPendingClearSaleStatuses; $j++) {
                $filter = $this->filterBuilder->setField('status')->setConditionType('eq')->setValue($pendingClearSaleStatuses[$j])->create();
                $filters[$i] = $filter;
                $i++;
            }
        }

        $filterGroup = $this->filterGroupBuilder->setFilters($filters)->create();
        $searchCriteria = $this->searchCriteriaBuilder->setFilterGroups([$filterGroup])->create();

        $orders = $this->orderRepository->getList($searchCriteria);

        $this->logger->info('Orders: ' . json_encode($orders));

        if ($orders) {
            foreach ($orders as $order) {
                $this->logger->info('Order id: ' . $order->getId());
                try
                {
                    $storeId = $order->getStoreId();
                    $orderStatus = $order->getStatus();
                    $payment = $order->getPayment();

                    $this->logger->info('Order Status: ' . $orderStatus);

                    $isActive = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
                    $pendingClearSaleStatuses = explode(",", $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/pending_clearsale', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId));
                    $creditcardMethods = explode(",", $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/credicardmethod', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId));

                    if ($isActive && in_array($orderStatus, $pendingClearSaleStatuses) && !$this->orderExists($order) && in_array($payment->getMethodInstance()->getCode(), $creditcardMethods)) {
                        $orderId = $order->getId();
                        $response = $this->sendSpecificOrder($order);

                        $this->logger->info('Response: ' . $response->HttpCode);
                        if ($response->HttpCode == 200) {
                            $orderResponse = json_decode($response->Body);

                            if ($orderResponse) {
                                if (!empty($orderResponse->Orders)) {
                                    $orderStatus = new Status();
                                    $orderStatus->order = $order;
                                    $orderStatus->Status = $orderResponse->Orders[0]->Status;
                                    $this->objectObject->update($orderStatus, $storeId);
                                }
                            }
                        } else if (strpos(strtolower($response->Body), "exists") !== false) {
                            $this->logger->info('atualizar pedido');
                            $orderStatus = new Status();
                            $orderStatus->order = $order;
                            $orderStatus->Status = 'AMA';
                            $this->objectObject->update($orderStatus, $storeId);
                            $this->logger->info('atualizar pedido - OK');
                        }
                    }
                } catch (\Exception $e) {
                    $this->logger->info($e->getMessage());
                }
            }
        }
    }

    public function sendSingleOrder($order)
    {
        $storeId = $order->getStoreId();

        $isActive = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);

        if ($isActive) {
            $this->logger->info('Method sendSingleOrder called...');

            $this->logger->info('Order id: ' . $order->getId());
            try
            {
                $orderId = $order->getId();
                $response = $this->sendSpecificOrder($order);

                $this->logger->info('Response: ' . $response->HttpCode);

                if ($response->HttpCode == 200) {
                    $orderResponse = json_decode($response->Body);

                    if ($orderResponse) {
                        if (!empty($orderResponse->Orders)) {
                            $orderStatus = new Status();
                            $orderStatus->order = $order;
                            $orderStatus->Status = $orderResponse->Orders[0]->Status;
                            $this->objectObject->update($orderStatus, $storeId);
                        }
                    }
                } else if (strpos(strtolower($response->Body), "exists") !== false) {
                    $this->logger->info('atualizar pedido');
                    $orderStatus = new Status();
                    $orderStatus->order = $order;
                    $orderStatus->Status = 'AMA';
                    $this->objectObject->update($orderStatus, $storeId);
                    $this->logger->info('atualizar pedido - OK');
                }
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
            }
        }
    }

    public function sendSpecificOrder($order)
    {
        try
        {
            if (!empty($order)) {
                $storeId = $order->getStoreId();

                $isActive = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);

                if ($isActive) {
                    $isReanalysis = false;
                    $storeId = $order->getStoreId();
                    $payment = $order->getPayment();

                    $environment = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/environment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
                    $analysisLocation = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/analysislocation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
                    $CreditcardMethods = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/credicardmethod', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);

                    $authResponse = $this->auth->login($environment, $storeId);
                    $clearSaleOrder = $this->toClearsaleOrderObject($order, $isReanalysis, $analysisLocation);
                    $requestOrder = new RequestOrder();
                    $requestOrder->ApiKey = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
                    $requestOrder->LoginToken = $authResponse->Token->Value;
                    $requestOrder->AnalysisLocation = $analysisLocation;
                    $requestOrder->Orders[0] = $clearSaleOrder;
                    $response = $this->objectObject->send($requestOrder, $environment);

                    $this->logger->info('<br />Enviar Order ->' . $order->getId() . ' OK ');

                    return $response;
                }
            }
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }

    public function toClearsaleOrderObject($order, $isReanalysis, $location)
    {
        try {
            $storeId = $order->getStoreId();

            $email = $order->getCustomerEmail();

            if (!$email) {
                $email = $order->getBillingAddress()->getEmail();
            }

            $currency = "USD";
            $legalDocument = "";

            $date = new \DateTime($order->getCreatedAt());
            $date = date('c', strtotime($order->getCreatedAt()));

            $clearsaleOrder = $this->orderFactory->create();
            $clearsaleOrder->ID = $order->getRealOrderId();
            try {
                $clearsaleOrder->IP = $order->getRemoteIp();
            } catch (\Exception $e) {
                $clearsaleOrder->IP = $order->getXForwardedFor();
            }
            $clearsaleOrder->Currency = $currency;
            $clearsaleOrder->Date = $date;
            $clearsaleOrder->Reanalysis = $isReanalysis;
            $clearsaleOrder->Email = $email;
            $clearsaleOrder->TotalOrder = number_format(floatval($order->getGrandTotal()), 2, ".", "");

            $items = $order->getAllItems();
            $payment = $order->getPayment();

            $billingAddress = $order->getBillingAddress();
            $shippingAddress = $order->getShippingAddress();

            //$dob = $customer->getDob();

            $dob = $date;

            if (!$billingAddress) {
                $billingAddress = $shippingAddress;
            }

            if (!$shippingAddress) {
                $shippingAddress = $billingAddress;
            }

            $billingName = $billingAddress->getFirstname() . " " . $billingAddress->getMiddlename() . " " . $billingAddress->getLastname();
            $billingName = trim(str_replace("  ", " ", $billingName));

            $billingCountry = $billingAddress->getCountryId();
            $billingPhone = preg_replace('/[^0-9]/', '', $billingAddress->getTelephone());

            $shippingName = $shippingAddress->getFirstname() . " " . $shippingAddress->getMiddlename() . " " . $shippingAddress->getLastname();
            $shippingName = trim(str_replace("  ", " ", $shippingName));
            $shippingCountry = $shippingAddress->getCountryId();
            $shippingPhone = preg_replace('/[^0-9]/', '', $shippingAddress->getTelephone());

            $paymentType = 1;
            $creditcardBrand = 0;
            $paymentIndex = 0;

            $creditcardMethods = explode(",", $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/credicardmethod', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId));

            $clearsaleOrder->Payments[$paymentIndex] = new \Clearsale\Integration\Model\Order\Entity\Payment();
            $clearsaleOrder->Payments[$paymentIndex]->Amount = number_format(floatval($order->getGrandTotal()), 2, ".", "");
            $clearsaleOrder->Payments[$paymentIndex]->Type = 14;
            $clearsaleOrder->Payments[$paymentIndex]->CardType = 4;
            $clearsaleOrder->Payments[$paymentIndex]->Date = $date;

            $payment->setAdditionalInformation('clearsale', 'ok');

            try {
                if ($order->getPayment()->getCcLast4()) {
                    $clearsaleOrder->Payments[$paymentIndex]->CardEndNumber = $order->getPayment()->getCcLast4();
                    $clearsaleOrder->Payments[$paymentIndex]->PaymentTypeID = 1;
                    $clearsaleOrder->Payments[$paymentIndex]->Type = 1;
                }
            } catch (\Exception $e) {
                $csLog->log($e->getMessage());
            }

            $customFieldIndex = 0;

            if ($order->getShippingDescription()) {
                $clearsaleOrder->CustomFields[$customFieldIndex] = new \Clearsale\Integration\Model\Order\Entity\CustomField();
                $clearsaleOrder->CustomFields[$customFieldIndex]->Type = 1;
                $clearsaleOrder->CustomFields[$customFieldIndex]->Name = 'SHIPPING_TYPE';
                $clearsaleOrder->CustomFields[$customFieldIndex]->Value = $order->getShippingDescription();
                $customFieldIndex = $customFieldIndex + 1;
            }

            if ($order->getPayment()->getMethod() == 'anet_creditcard') {
                $clearsaleOrder->CustomFields[$customFieldIndex] = new \Clearsale\Integration\Model\Order\Entity\CustomField();
                $clearsaleOrder->CustomFields[$customFieldIndex]->Type = 1;
                $clearsaleOrder->CustomFields[$customFieldIndex]->Name = 'TRANSACTION_ID';
                $clearsaleOrder->CustomFields[$customFieldIndex]->Value = $order->getPayment()->getTransactionId();
            }

            $countryname = $billingAddress->getCountryId();

            $clearsaleOrder->BillingData = $this->personFactory->create();
            $clearsaleOrder->BillingData->ID = $order->getId();
            $clearsaleOrder->BillingData->Email = $email;
            $clearsaleOrder->BillingData->LegalDocument = $legalDocument;
            $clearsaleOrder->BillingData->BirthDate = $dob;
            $clearsaleOrder->BillingData->Name = $billingName;
            $clearsaleOrder->BillingData->Type = 1;
            $clearsaleOrder->BillingData->Gender = 'M';
            $clearsaleOrder->BillingData->Address->City = $billingAddress->getCity();
            $clearsaleOrder->BillingData->Address->Country = $countryname;
            $clearsaleOrder->BillingData->Address->Street = $billingAddress->getStreet(1)[0];
            $clearsaleOrder->BillingData->Address->Comp = $billingAddress->getStreet(2)[0];
            //if($billingAddress->getStreet(4))
            //{
            //  $clearsaleOrder->BillingData->Address->County = $billingAddress->getStreet(4);
            //}

            $arr = explode(' ', trim($billingAddress->getStreetFull()));
            $clearsaleOrder->BillingData->Address->Number = $arr[0];
            if ($shippingAddress->getRegion()) {
                $clearsaleOrder->BillingData->Address->State = $shippingAddress->getRegion();
            } else {
                $clearsaleOrder->BillingData->Address->State = "**";
            }

            $zipcodeBilling = preg_replace('/[^0-9]/', '', $billingAddress->getPostcode());

            if ($zipcodeBilling) {
                $clearsaleOrder->BillingData->Address->ZipCode = $zipcodeBilling;
            } else {
                $clearsaleOrder->BillingData->Address->ZipCode = "XXX";
            }

            if ($location == "BRA") {
                $clearsaleOrder->BillingData->Phones[0] = $this->phoneFactory->create();
                $clearsaleOrder->BillingData->Phones[0]->AreaCode = substr($billingPhone, 0, 2);
                $clearsaleOrder->BillingData->Phones[0]->Number = substr($billingPhone, 2, 9);
                $clearsaleOrder->BillingData->Phones[0]->CountryCode = "55";
                $clearsaleOrder->BillingData->Phones[0]->Type = 1;
            } else {
                $clearsaleOrder->BillingData->Phones[0] = $this->phoneFactory->create();
                $clearsaleOrder->BillingData->Phones[0]->AreaCode = substr($billingPhone, 0, 3);
                $clearsaleOrder->BillingData->Phones[0]->Number = $billingPhone;
                $clearsaleOrder->BillingData->Phones[0]->CountryCode = "1";
                $clearsaleOrder->BillingData->Phones[0]->Type = 1;
            }

            $countryname = $shippingAddress->getCountryId();

            $clearsaleOrder->ShippingData = $this->personFactory->create();
            $clearsaleOrder->ShippingData->ID = "1";
            $clearsaleOrder->ShippingData->Email = $email;
            $clearsaleOrder->ShippingData->LegalDocument = $legalDocument;
            $clearsaleOrder->ShippingData->BirthDate = $dob;
            $clearsaleOrder->ShippingData->Name = $shippingName;
            $clearsaleOrder->ShippingData->Gender = 'M';
            $clearsaleOrder->ShippingData->Type = 1;

            $clearsaleOrder->ShippingData->Address->City = $shippingAddress->getCity();
            $clearsaleOrder->ShippingData->Address->Country = $countryname;
            $clearsaleOrder->ShippingData->Address->Street = $shippingAddress->getStreet(1)[0];
            $clearsaleOrder->ShippingData->Address->Comp = $shippingAddress->getStreet(2)[0];

            //if($shippingAddress->getStreet(4))
            //{
            //  $clearsaleOrder->ShippingData->Address->County = $shippingAddress->getStreet(4);
            //}
            $arr = explode(' ', trim($shippingAddress->getStreetFull()));
            $clearsaleOrder->ShippingData->Address->Number = $arr[0];

            $shippingState = $shippingAddress->getRegion();

            if ($shippingState) {
                $clearsaleOrder->ShippingData->Address->State = $shippingState;
            } else {
                $clearsaleOrder->ShippingData->Address->State = "**";
            }

            $zipcodeShipping = preg_replace('/[^0-9]/', '', $shippingAddress->getPostcode());

            if ($zipcodeShipping) {
                $clearsaleOrder->ShippingData->Address->ZipCode = $zipcodeShipping;
            } else {
                $clearsaleOrder->ShippingData->Address->ZipCode = "XXX";
            }

            if ($location == "BRA") {
                $clearsaleOrder->ShippingData->Phones[0] = $this->phoneFactory->create();
                $clearsaleOrder->ShippingData->Phones[0]->AreaCode = substr($shippingPhone, 0, 2);
                $clearsaleOrder->ShippingData->Phones[0]->Number = substr($shippingPhone, 2, 9);
                $clearsaleOrder->ShippingData->Phones[0]->CountryCode = "55";
                $clearsaleOrder->ShippingData->Phones[0]->Type = 1;
            } else {
                $clearsaleOrder->ShippingData->Phones[0] = $this->phoneFactory->create();
                $clearsaleOrder->ShippingData->Phones[0]->AreaCode = substr($shippingPhone, 0, 3);
                $clearsaleOrder->ShippingData->Phones[0]->Number = $shippingPhone;
                $clearsaleOrder->ShippingData->Phones[0]->CountryCode = "1";
                $clearsaleOrder->ShippingData->Phones[0]->Type = 1;
            }

            $itemIndex = 0;
            $TotalItems = 0;

            foreach ($items as $item) {
                $clearsaleOrder->Items[$itemIndex] = $this->itemFactory->create();
                $clearsaleOrder->Items[$itemIndex]->Price = number_format(floatval($item->getPrice()), 2, ".", "");
                $clearsaleOrder->Items[$itemIndex]->ProductId = $item->getSku();
                $clearsaleOrder->Items[$itemIndex]->ProductTitle = $item->getName();
                $clearsaleOrder->Items[$itemIndex]->Quantity = intval($item->getQtyOrdered());
                $TotalItems += $clearsaleOrder->Items[$itemIndex]->Price;
                $itemIndex++;
            }

            $clearsaleOrder->TotalOrder = $order->getGrandTotal();
            $clearsaleOrder->TotalItems = $TotalItems;
            $clearsaleOrder->TotalShipping = $order->getShippingInclTax();

            $sessionID = $payment->getAdditionalInformation('clearsaleSessionID');
            $clearsaleOrder->SessionID = $sessionID;

            return $clearsaleOrder;

        } catch (\Exception $e) {
            $this->logger->info('Ex:' . $e->getMessage());
        }
    }

    public function getCountryname($countryCode)
    {
        $country = $this->countryFactory->create()->loadByCode($countryCode);
        return $country->getName();
    }

    public function getClearsaleOrderStatus()
    {
        $this->logger->info('Update order Status called.');

        $storeIds = $this->getStoreIDs();
        $filters = [];
        $i = 0;

        foreach ($storeIds as $storeId) {
            $analyzing_clearsale = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/analyzing_clearsale', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);

            $filter = $this->filterBuilder->setField('status')->setConditionType('eq')->setValue($analyzing_clearsale)->create();
            $filters[$i] = $filter;
            $i++;
        }

        $filterGroup = $this->filterGroupBuilder->setFilters($filters)->create();
        $searchCriteria = $this->searchCriteriaBuilder->setFilterGroups([$filterGroup])->create();

        $orders = $this->orderRepository->getList($searchCriteria);

        if ($orders) {
            foreach ($orders as $order) {
                $storeId = $order->getStoreId();

                $isActive = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
                $analyzing_clearsale = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/analyzing_clearsale', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);

                if ($isActive && $order->getStatus() == $analyzing_clearsale) {
                    $environment = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/environment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);

                    $analysisLocation = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/analysislocation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);

                    $authResponse = $this->auth->login($environment, $storeId);

                    if ($authResponse) {
                        $orderId = $order->getRealOrderId();

                        $requestOrder = new RequestOrder();
                        $requestOrder->ApiKey = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
                        $requestOrder->LoginToken = $authResponse->Token->Value;
                        $requestOrder->AnalysisLocation = $analysisLocation;

                        $requestOrder->Orders = array();
                        $requestOrder->Orders[0] = $orderId;
                        $response = $this->objectObject->get($requestOrder, $environment);

                        if ($response->HttpCode == 200) {

                            $responseOrder = json_decode($response->Body);
                            $orderStatus = new Status();
                            $orderStatus->order = $order;
                            $orderStatus->Status = $responseOrder->Orders[0]->Status;

                            $this->objectObject->update($orderStatus, $storeId);

                            $order = $order->loadByIncrementId($orderId);

                            if ($order->getStatus() == 'approved_clearsale') {
                                $createInvoice = $this->clearSaleTotalConfig->getValue('clearsale_configuration/cs_config/create_invoice', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
                                if ($createInvoice) {
                                    $this->createInvoice($order);
                                    $this->logger->info('Create Invoice: ' . $order->getId() . ' OK ');
                                }

                            }
                        }
                    }
                }
            }
        }
    }

    public function createInvoice($order)
    {
        if ($order->canInvoice()) {
            $invoice = $this->invoiceService->prepareInvoice($order);
            $invoice->register();
            $invoice->addComment("Invoice auto created by Clearsale approvement configuration.", false, false);
            $invoice->save();
            $transactionSave = $this->transaction->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );

            $transactionSave->save();

            //  $this->invoiceSender->send($invoice);  --no errors, halting
            //send notification code

            //  $order->addStatusHistoryComment('Notified customer about invoice '. $invoice->getId())
            //      ->setIsCustomerNotified(true)
            //      ->save();

        }
    }
}
