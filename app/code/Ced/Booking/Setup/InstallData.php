<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Booking
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Booking\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    private $bookingSetupFactory;

    const ENTITY_TYPE = \Magento\Catalog\Model\Product::ENTITY;

    /**
     * InstallData constructor.
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        \Ced\Booking\Helper\Data $helperData
    )

    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->_eavAttribute = $eavAttribute;
        $this->helperData = $helperData;
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup ();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $defaultId = $eavSetup->getDefaultAttributeSetId(self::ENTITY_TYPE);

        $entityTypeId = $eavSetup->getEntityTypeId(self::ENTITY_TYPE);

        $groupName = 'Booking General Information';

        $eavSetup->addAttributeGroup($entityTypeId,$defaultId,$groupName,10);

        $bookingTypes = implode(',', $this->helperData->getAllBookingTypes());


        $eavSetup->addAttribute('catalog_product', 'booking_location', [
                'group'            => $groupName,
                'note'             => '',
                'input'            => 'input',
                'type'             => 'text',
                'label'            => 'Location',
                'backend'          => '',
                'visible'          => true,
                'required'         => false,
                'sort_order'       => 100,
                'user_defined'     => 1,
                'source'           => '',
                'comparable'       => 0,
                'visible_on_front' => 0,
                'global'           => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'apply_to'         => $bookingTypes
            ]
        );

        $eavSetup->addAttribute('catalog_product', 'booking_policy', [
                'group'            => $groupName,
                'note'             => '',
                'input'            => 'textarea',
                'type'             => 'text',
                'label'            => 'Booking Policy',
                'backend'          => '',
                'visible'          => true,
                'required'         => false,
                'sort_order'       => 110,
                'user_defined'     => 1,
                'source'           => '',
                'comparable'       => 0,
                'visible_on_front' => 0,
                'is_html_allowed_on_front' => true,
                'wysiwyg_enabled' => true,
                'global'           => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'apply_to'         => $bookingTypes
            ]
        );

        $eavSetup->addAttribute('catalog_product', 'booking_terms_and_condition', [
                'group'            => $groupName,
                'note'             => '',
                'input'            => 'textarea',
                'type'             => 'text',
                'label'            => 'Booking Terms and Condition',
                'backend'          => '',
                'visible'          => true,
                'required'         => false,
                'sort_order'       => 120,
                'user_defined'     => 1,
                'source'           => '',
                'comparable'       => 0,
                'visible_on_front' => 0,
                'is_html_allowed_on_front' => true,
                'wysiwyg_enabled' => true,
                'global'           => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'apply_to'         => $bookingTypes
            ]
        );

        $eavSetup->addAttribute('catalog_product', 'non_working_dates', [
                'group'            => $groupName,
                'note'             => '',
                'input'            => 'text',
                'type'             => 'text',
                'label'            => 'Non Working Dates',
                'backend'          => '',
                'visible'          => false,
                'required'         => false,
                'sort_order'       => 190,
                'user_defined'     => 1,
                'source'           => '',
                'comparable'       => 0,
                'visible_on_front' => 0,
                'global'           => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'apply_to'         => $bookingTypes
            ]
        );

        $eavSetup->addAttribute('catalog_product', 'facility_ids', [
                'group'            => $groupName,
                'note'             => '',
                'input'            => 'text',
                'type'             => 'text',
                'label'            => 'Facility Ids',
                'backend'          => '',
                'visible'          => false,
                'required'         => false,
                'sort_order'       => 190,
                'user_defined'     => 1,
                'source'           => '',
                'comparable'       => 0,
                'visible_on_front' => 0,
                'global'           => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'apply_to'         => $bookingTypes
            ]
        );
    }
}

