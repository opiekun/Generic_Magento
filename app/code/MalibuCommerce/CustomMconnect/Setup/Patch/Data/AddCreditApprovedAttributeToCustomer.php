<?php

namespace MalibuCommerce\CustomMconnect\Setup\Patch\Data;

use Magento\Catalog\Setup\CustomerSetup;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use MalibuCommerce\CustomMconnect\Helper\CustomerCustomAttribute;

class AddCreditApprovedAttributeToCustomer implements DataPatchInterface, PatchRevertableInterface
{
    const CUSTOM_ATTRIBUTE_CREDIT_APPROVED_EAV_CONFIG = [
        'type'             => 'int',
        'label'            => 'Credit Approved',
        'input'            => 'boolean',
        'global'           => ScopedAttributeInterface::SCOPE_GLOBAL,
        'source'           => Boolean::class,
        'required'         => false,
        'visible'          => false,
        'visible_on_front' => false,
        'user_defined'     => false,
        'system'           => false,
        'default'          => 0,
    ];

    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

    /**
     * @param CustomerSetupFactory $customerSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Run code inside patch
     *
     * @return AddCreditApprovedAttributeToCustomer
     *
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function apply()
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $entityTypeId = $customerSetup->getEntityTypeId(Customer::ENTITY);

        if (!empty($customerSetup->getAttribute(
            $entityTypeId,
            CustomerCustomAttribute::CUSTOM_ATTRIBUTE_CREDIT_APPROVED_EAV_CODE
        ))) {
            return $this;
        }
        $customerSetup->addAttribute(
            Customer::ENTITY,
            CustomerCustomAttribute::CUSTOM_ATTRIBUTE_CREDIT_APPROVED_EAV_CODE,
            self::CUSTOM_ATTRIBUTE_CREDIT_APPROVED_EAV_CONFIG
        );

        $attributeSetId = $customerSetup->getDefaultAttributeSetId($entityTypeId);
        $attributeGroupId = $customerSetup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);
        $attribute = $customerSetup->getAttribute(
            $entityTypeId,
            CustomerCustomAttribute::CUSTOM_ATTRIBUTE_CREDIT_APPROVED_EAV_CODE
        );
        if (empty($attribute)) {
            return $this;
        }
        $customerSetup->addAttributeToGroup(
            $entityTypeId,
            $attributeSetId,
            $attributeGroupId,
            $attribute['attribute_id'],
            1000
        );

        return $this;
    }

    /**
     * Rollback all changes, done by this patch
     *
     * @return void
     *
     * @throws LocalizedException
     */
    public function revert()
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $entityTypeId = $customerSetup->getEntityTypeId(Customer::ENTITY);
        $customerSetup->removeAttribute(
            $entityTypeId,
            CustomerCustomAttribute::CUSTOM_ATTRIBUTE_CREDIT_APPROVED_EAV_CODE
        );
    }

    /**
     * Get aliases (previous names) for the patch.
     *
     * @return string[]
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * Get array of patches that have to be executed prior to this.
     *
     * @return string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }
}
