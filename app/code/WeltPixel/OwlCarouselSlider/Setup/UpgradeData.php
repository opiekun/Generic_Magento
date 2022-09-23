<?php

namespace WeltPixel\OwlCarouselSlider\Setup;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    private $catalogSetupFactory;

    /**
     * Init
     *
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(CategorySetupFactory $categorySetupFactory)
    {
        $this->catalogSetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
        $catalogSetup = $this->catalogSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.1.0') < 0) {

            $attributeName = 'weltpixel_hover_image';

            if(!$catalogSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, $attributeName)){
                $catalogSetup->addAttribute(Product::ENTITY, $attributeName, [
                    'type' => 'varchar',
                    'label' => 'Listing Hover Image',
                    'input' => 'media_image',
                    'frontend' => 'Magento\Catalog\Model\Product\Attribute\Frontend\Image',
                    'required' => false,
                    'sort_order' => 4,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'used_in_product_listing' => true
                ]);
            }
        }

        $setup->endSetup();
    }
}