<?php

declare(strict_types=1);

namespace Ecommerce121\Customer\Plugin\Magento\Customer\Controller\Account;

use Magento\Customer\Controller\Account\LoginPost as MagentoLoginPost;
use Magento\Framework\Controller\Result\Redirect;

class LoginPost
{
    /**
     * @param MagentoLoginPost $subject
     * @param Redirect $result
     * @return Redirect
     */
    public function afterExecute(
        MagentoLoginPost $subject,
        $result
    ): Redirect
    {
        $result->setPath('/');

        return $result;
    }

}
