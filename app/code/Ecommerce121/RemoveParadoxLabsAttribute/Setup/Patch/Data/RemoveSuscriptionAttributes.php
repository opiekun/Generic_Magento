<?php

declare(strict_types=1);

namespace Ecommerce121\RemoveParadoxLabsAttribute\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class RemoveSuscriptionAttributes implements DataPatchInterface
{
    const ATTRIBUTES_TO_DELETE = [
        'subscription_active',
        'subscription_allow_onetime',
        'subscription_intervals',
        'subscription_unit',
        'subscription_length',
        'subscription_price',
        'subscription_init_adjustment'
    ];

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private EavSetupFactory $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @return RemoveSuscriptionAttributes|void
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        foreach (self::ATTRIBUTES_TO_DELETE as $attribute_code) {
            if ($eavSetup->getAttribute(Product::ENTITY, $attribute_code)) {
                $eavSetup->removeAttribute(Product::ENTITY, $attribute_code);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Get aliases (previous names) for the patch.
     */
    public function getAliases(): array
    {
        return [];
    }
}
