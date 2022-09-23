<?php

declare(strict_types=1);

namespace Ecommerce121\CompanyCustomer\Setup\Patch\Data;

use Magento\Customer\Model\Indexer\Address\AttributeProvider;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdateCompanyAttribute implements DataPatchInterface
{
    const ATTRIBUTE_CODE = 'company';

    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private EavSetupFactory $eavSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetup = $eavSetup;
    }

    /**
     * @return UpdateCompanyAttribute
     */
    public function apply(): UpdateCompanyAttribute
    {
        /**
         * @var $eavSetup EavSetup
         */
        $eavSetup = $this->eavSetup->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->updateAttribute(
            AttributeProvider::ENTITY,
            self::ATTRIBUTE_CODE,
            'is_system',
            '0'
        );
        $this->moduleDataSetup->endSetup();

        return $this;
    }

    /**
     * @return string[]
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }
}
