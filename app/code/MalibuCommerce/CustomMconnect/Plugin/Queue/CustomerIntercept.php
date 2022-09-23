<?php

namespace MalibuCommerce\CustomMconnect\Plugin\Queue;

use MalibuCommerce\CustomMconnect\Helper\CustomerCustomAttribute;
use MalibuCommerce\MConnect\Model\Queue\Customer as Subject;

class CustomerIntercept
{
    /**
     * Set customer custom attributes
     *
     * @param Subject $queueCustomer
     * @param Subject $result
     *
     * @return Subject
     */
    public function afterInitImport(Subject $queueCustomer, Subject $result)
    {
        foreach (CustomerCustomAttribute::MAP_EAV_TO_NAV_CUSTOM_ATTRIBUTE as $eavAttributeCode => $navAttributeCode) {
            $queueCustomer->mapEavToNavCustomCustomerAttribute($eavAttributeCode, $navAttributeCode);
        }

        return $result;
    }
}
