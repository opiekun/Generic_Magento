<?php

namespace WeltPixel\GoogleTagManager\Plugin;

class GuestPaymentInformation
{
    /**
     * @var \WeltPixel\GoogleTagManager\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @param \WeltPixel\GoogleTagManager\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        \WeltPixel\GoogleTagManager\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository)
    {
        $this->helper = $helper;
        $this->_checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param \Magento\Checkout\Model\GuestPaymentInformationManagement $subject
     * @return int Order ID.
     */
    public function afterSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Model\GuestPaymentInformationManagement $subject,
        $result
        )
    {
        if (!$this->helper->isEnabled()) {
            return $result;
        }

        $orderId = $result;

        $order = $this->_checkoutSession->getLastRealOrder();
        if (!$order->getId()) {
            try {
                $order = $this->orderRepository->get($orderId);
            } catch (\Exception $ex) {
                return $result;
            }
        }

        $additionalInformation = $order->getPayment()->getAdditionalInformation();

        if ($additionalInformation && isset($additionalInformation['method_title'])) {
            $paymentMethodTitle = $additionalInformation['method_title'];
            $this->_checkoutSession->setCheckoutOptionsData($this->helper->addCheckoutStepPushData('3', $paymentMethodTitle));
        }

        return $result;
    }


}
