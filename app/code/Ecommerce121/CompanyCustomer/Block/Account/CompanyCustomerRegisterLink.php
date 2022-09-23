<?php

declare(strict_types=1);

namespace Ecommerce121\CompanyCustomer\Block\Account;

use Magento\Customer\Model\Context;
use Magento\Customer\Block\Account\RegisterLink;

class CompanyCustomerRegisterLink extends RegisterLink
{
    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('company_customer/account/create');
    }
}
