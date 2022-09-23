<?php

namespace Ecommerce121\CompanyCustomer\Block\Form\Login;

use Magento\Framework\View\Element\Template;

/**
 * Customer login info block
*/
class Info extends Template
{
    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('company_customer/account/create');
    }
}
