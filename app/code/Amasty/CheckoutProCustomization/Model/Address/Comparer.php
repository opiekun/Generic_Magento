<?php
declare(strict_types=1);

namespace Amasty\CheckoutProCustomization\Model\Address;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\Address;

class Comparer
{
    /**
     * @var array
     */
    private $addressFields;
    
    public function __construct(
        array $addressFields = [
            AddressInterface::PREFIX,
            AddressInterface::FIRSTNAME,
            AddressInterface::MIDDLENAME,
            AddressInterface::LASTNAME,
            AddressInterface::SUFFIX,
            AddressInterface::COMPANY,
            AddressInterface::STREET,
            AddressInterface::CITY,
            AddressInterface::REGION,
            AddressInterface::POSTCODE,
            AddressInterface::COUNTRY_ID,
            AddressInterface::TELEPHONE,
            AddressInterface::FAX,
            AddressInterface::VAT_ID
        ]
    ) {
        $this->addressFields = $addressFields;
    }

    /**
     * Compare addresses by a certain set of values which displayed on customer page from back-end
     * see vendor/magento/module-customer/view/adminhtml/web/template/default-address.html
     *
     * @param Address $address1
     * @param Address $address2
     * @return bool
     */
    public function isEqual(Address $address1, Address $address2): bool
    {
        foreach ($this->addressFields as $fieldName) {
            $fieldValue1 = $address1->getData($fieldName);
            $fieldValue2 = $address2->getData($fieldName);
            
            if ($fieldValue1 != $fieldValue2) {
                return false;
            }
        }
        
        return true;
    }
}
