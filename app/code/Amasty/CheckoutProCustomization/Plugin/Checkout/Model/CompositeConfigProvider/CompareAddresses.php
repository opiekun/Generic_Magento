<?php
declare(strict_types=1);

namespace Amasty\CheckoutProCustomization\Plugin\Checkout\Model\CompositeConfigProvider;

use Amasty\Checkout\Model\CheckoutConfigProvider\Address as CheckoutAddressConfigProvider;
use Amasty\CheckoutProCustomization\Model\Address\Comparer;
use Magento\Checkout\Model\CompositeConfigProvider;
use Magento\Customer\Model\Session;

class CompareAddresses
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var Comparer
     */
    private $comparer;

    public function __construct(
        Session $session,
        Comparer $comparer
    ) {
        $this->session = $session;
        $this->comparer = $comparer;
    }

    /**
     * @param CompositeConfigProvider $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetConfig(CompositeConfigProvider $subject, array $result): array
    {
        if (!$this->session->isLoggedIn()
            || !$result[CheckoutAddressConfigProvider::IS_BILLING_SAME_AS_SHIPPING]
        ) {
            return $result;
        }

        $customer = $this->session->getCustomer();
        $defaultBilling = $customer->getDefaultBillingAddress();
        $defaultShipping = $customer->getDefaultShippingAddress();

        if ($defaultBilling && $defaultShipping) {
            $result[CheckoutAddressConfigProvider::IS_BILLING_SAME_AS_SHIPPING] = $this->comparer->isEqual(
                $defaultBilling,
                $defaultShipping
            );
        }

        return $result;
    }
}
