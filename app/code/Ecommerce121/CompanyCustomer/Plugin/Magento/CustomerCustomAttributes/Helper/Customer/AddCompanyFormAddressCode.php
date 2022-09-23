<?php

declare(strict_types=1);

namespace Ecommerce121\CompanyCustomer\Plugin\Magento\CustomerCustomAttributes\Helper\Customer;

use Magento\CustomerCustomAttributes\Helper\Address;

class AddCompanyFormAddressCode
{
    /**
     * @param Customer $subject
     * @param array $result
     * @return array
     */
    public function afterGetAttributeFormOptions(Address $subject, array $result)
    {
        $result[] = ['label' => __('Company Customer Registration'), 'value' => 'company_customer_account_create'];

        return $result;
    }
}
