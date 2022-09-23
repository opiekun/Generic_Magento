<?php

namespace MalibuCommerce\CustomMconnect\Helper;

class CustomerCustomAttribute
{
    const CUSTOM_ATTRIBUTE_CREDIT_APPROVED_EAV_CODE = 'credit_approved';
    const CUSTOM_ATTRIBUTE_CREDIT_APPROVED_NAV_CODE = 'cust_credit_approved';

    const CUSTOM_ATTRIBUTE_SHIP_COMPLETE_EAV_CODE = 'ship_complete';
    const CUSTOM_ATTRIBUTE_SHIP_COMPLETE_NAV_CODE = 'cust_ship_complete';

    const MAP_EAV_TO_NAV_CUSTOM_ATTRIBUTE = [
        self::CUSTOM_ATTRIBUTE_CREDIT_APPROVED_EAV_CODE => self::CUSTOM_ATTRIBUTE_CREDIT_APPROVED_NAV_CODE,
        self::CUSTOM_ATTRIBUTE_SHIP_COMPLETE_EAV_CODE   => self::CUSTOM_ATTRIBUTE_SHIP_COMPLETE_NAV_CODE,
    ];
}
