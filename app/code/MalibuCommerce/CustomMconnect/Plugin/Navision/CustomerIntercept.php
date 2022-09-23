<?php

namespace MalibuCommerce\CustomMconnect\Plugin\Navision;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Exception\LocalizedException;
use MalibuCommerce\CustomMconnect\Helper\CustomerCustomAttribute;
use MalibuCommerce\MConnect\Model\Navision\Customer as Subject;

class CustomerIntercept
{
    /**
     * Get customer custom attribute values
     *
     * @param Subject $navisionCustomer
     * @param \simpleXMLElement $result
     * @param CustomerInterface $customer
     * @param Customer $customerDataModel
     * @param \simpleXMLElement $exportXml
     *
     * @return \simpleXMLElement
     *
     * @throws LocalizedException
     */
    public function afterSetCustomCustomerAttributes(
        Subject $navisionCustomer,
        \simpleXMLElement $result,
        CustomerInterface $customer,
        Customer $customerDataModel,
        \simpleXMLElement $exportXml
    ) {
        foreach (CustomerCustomAttribute::MAP_EAV_TO_NAV_CUSTOM_ATTRIBUTE as $eavAttributeCode => $navAttributeCode) {
            /** @var AbstractAttribute|false $attribute */
            $attribute = $customerDataModel->getAttribute($eavAttributeCode);
            if (empty($attribute)) {
                continue;
            }
            /** @var AttributeInterface|null $customAttribute */
            $customAttribute = $customer->getCustomAttribute($eavAttributeCode);
            if (empty($customAttribute)) {
                continue;
            }
            $result->$navAttributeCode = $attribute->usesSource()
                ? (string)$attribute->getSource()->getOptionText($customAttribute->getValue())
                : $customAttribute->getValue();
        }

        return $result;
    }
}
