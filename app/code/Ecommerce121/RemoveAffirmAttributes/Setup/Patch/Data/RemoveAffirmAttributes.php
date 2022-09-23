<?php

declare(strict_types=1);

namespace Ecommerce121\RemoveAffirmAttributes\Setup\Patch\Data;


use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Category;
use Magento\Customer\Model\Customer;


class RemoveAffirmAttributes implements DataPatchInterface
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
     * Remove affirm integration attributes if they exist
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        if ($eavSetup->getAttribute(Product::ENTITY, 'affirm_product_mfp')) {
            $eavSetup->removeAttribute(Product::ENTITY, 'affirm_product_mfp');
        }
        if ($eavSetup->getAttribute(Product::ENTITY, 'affirm_product_mfp_type')) {
            $eavSetup->removeAttribute(Product::ENTITY, 'affirm_product_mfp_type');
        }
        if ($eavSetup->getAttribute(Product::ENTITY, 'affirm_product_mfp_priority')) {
            $eavSetup->removeAttribute(Product::ENTITY, 'affirm_product_mfp_priority');
        }
        if ($eavSetup->getAttribute(Product::ENTITY, 'affirm_product_mfp_start_date')) {
            $eavSetup->removeAttribute(Product::ENTITY, 'affirm_product_mfp_start_date');
        }
        if ($eavSetup->getAttribute(Product::ENTITY, 'affirm_product_mfp_end_date')) {
            $eavSetup->removeAttribute(Product::ENTITY, 'affirm_product_mfp_end_date');
        }
        if ($eavSetup->getAttribute(Product::ENTITY, 'affirm_product_promo_id')) {
            $eavSetup->removeAttribute(Product::ENTITY, 'affirm_product_promo_id');
        }
        if ($eavSetup->getAttribute(Category::ENTITY, 'affirm_category_mfp')) {
            $eavSetup->removeAttribute(Category::ENTITY, 'affirm_category_mfp');
        }
        if ($eavSetup->getAttribute(Category::ENTITY, 'affirm_category_mfp_type')) {
            $eavSetup->removeAttribute(Category::ENTITY, 'affirm_category_mfp_type');
        }
        if ($eavSetup->getAttribute(Category::ENTITY, 'affirm_category_mfp_priority')) {
            $eavSetup->removeAttribute(Category::ENTITY, 'affirm_category_mfp_priority');
        }
        if ($eavSetup->getAttribute(Category::ENTITY, 'affirm_category_mfp_start_date')) {
            $eavSetup->removeAttribute(Category::ENTITY, 'affirm_category_mfp_start_date');
        }
        if ($eavSetup->getAttribute(Category::ENTITY, 'affirm_category_mfp_end_date')) {
            $eavSetup->removeAttribute(Category::ENTITY, 'affirm_category_mfp_end_date');
        }
        if ($eavSetup->getAttribute(Category::ENTITY, 'affirm_category_promo_id')) {
            $eavSetup->removeAttribute(Category::ENTITY, 'affirm_category_promo_id');
        }
        if ($eavSetup->getAttribute(Customer::ENTITY, 'affirm_customer_mfp')) {
            $eavSetup->removeAttribute(Customer::ENTITY, 'affirm_customer_mfp');
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
