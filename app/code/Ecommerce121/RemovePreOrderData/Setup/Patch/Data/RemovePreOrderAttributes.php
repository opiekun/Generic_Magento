<?php

declare(strict_types=1);

namespace Ecommerce121\RemovePreOrderData\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\Product;

class RemovePreOrderAttributes implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $categorySetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Remove amasty_preorder_note and amasty_preorder_cart_label product attribute
     * if they exist
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        if ($eavSetup->getAttribute(Product::ENTITY, 'amasty_preorder_cart_label')) {
            $eavSetup->removeAttribute(Product::ENTITY, 'amasty_preorder_cart_label');
        }
        if ($eavSetup->getAttribute(Product::ENTITY, 'amasty_preorder_note')) {
            $eavSetup->removeAttribute(Product::ENTITY, 'amasty_preorder_note');
        }
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get aliases (previous names) for the patch.
     */
    public function getAliases()
    {
        return [];
    }
}
