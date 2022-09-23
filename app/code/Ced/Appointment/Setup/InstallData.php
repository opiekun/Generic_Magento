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
 * @package     Ced_Appointment
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Appointment\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface

{
    private $bookingSetupFactory;

    const ENTITY_TYPE = \Magento\Catalog\Model\Product::ENTITY;

    /**
     *
     * Init
     *
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
    )

    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->_eavAttribute = $eavAttribute;
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

        $appointmentSlotGroup = 'Appointment Slots';

        $eavSetup->addAttributeGroup($entityTypeId,$defaultId,$appointmentSlotGroup,10);

        $attributeId = $this->_eavAttribute->getIdByCode(self::ENTITY_TYPE, 'service_type');
        if (!$attributeId)
        {
            $eavSetup->addAttribute('catalog_product', 'service_type', [
                    'group'            => $appointmentSlotGroup,
                    'note'             => '',
                    'input'            => 'select',
                    'type'             => 'text',
                    'label'            => 'Service Type',
                    'backend'          => '',
                    'visible'          => true,
                    'required'         => true,
                    'sort_order'       => 130,
                    'user_defined'     => 1,
                    'source'           => '',
                    'comparable'       => 0,
                    'visible_on_front' => 0,
                    'option'           => ['values' => ['Branch', 'Home Service','Both']],
                    'global'           => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'apply_to'         => 'appointment'
                ]
            );
        }

        $eavSetup->addAttribute('catalog_product', 'duration', [
                'group'            => $appointmentSlotGroup,
                'note'             => __('[How long the appointment will be]'),
                'input'            => 'select',
                'type'             => 'text',
                'label'            => 'Duration',
                'backend'          => '',
                'visible'          => true,
                'required'         => true,
                'sort_order'       => 140,
                'user_defined'     => 1,
                'source'           => 'Ced\Appointment\Model\Source\Duration',
                'comparable'       => 0,
                'visible_on_front' => 0,
                'default'          => '60',
                'global'           => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'apply_to'         => 'appointment'
            ]
        );

        $eavSetup->addAttribute('catalog_product', 'qty_per_slot', [
                'group'            => $appointmentSlotGroup,
                'note'             => __('[No. of people allowed per appointment]'),
                'input'            => 'text',
                'type'             => 'text',
                'label'            => 'Qty per slot',
                'backend'          => '',
                'visible'          => true,
                'required'         => true,
                'sort_order'       => 150,
                'user_defined'     => 1,
                'source'           => '',
                'comparable'       => 0,
                'visible_on_front' => 0,
                'frontend_class' => 'validate-number',
                'global'           => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'apply_to'         => 'appointment'
            ]
        );

        /*$eavSetup->addAttribute('catalog_product', 'book_for_many_days', [
                'group'            => $appointmentSlotGroup,
                'note'             => __('[If yes, then customer can select multiple dates and multiple time slots for each day]'),
                'input'            => 'boolean',
                'type'             => 'int',
                'label'            => 'Book For Many Days',
                'backend'          => '',
                'visible'          => true,
                'required'         => true,
                'sort_order'       => 160,
                'user_defined'     => 1,
                'source'           => '',
                'comparable'       => 0,
                'visible_on_front' => 0,
                'global'           => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'apply_to'         => 'appointment'
            ]
        );*/

        $eavSetup->addAttribute('catalog_product', 'same_slot_all_week_days', [
                'group'            => $appointmentSlotGroup,
                'note'             => '',
                'input'            => 'boolean',
                'type'             => 'int',
                'label'            => 'Same slot for all week days',
                'backend'          => '',
                'visible'          => true,
                'required'         => true,
                'sort_order'       => 170,
                'user_defined'     => 1,
                'source'           => '',
                'comparable'       => 0,
                'visible_on_front' => 0,
                'global'           => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'apply_to'         => 'appointment'
            ]
        );

        $eavSetup->addAttribute('catalog_product', 'appointment_slots', [
                'group'            => $appointmentSlotGroup,
                'note'             => '',
                'input'            => 'text',
                'type'             => 'text',
                'label'            => 'Appointment Slots',
                'backend'          => '',
                'visible'          => false,
                'required'         => false,
                'sort_order'       => 180,
                'user_defined'     => 1,
                'source'           => '',
                'comparable'       => 0,
                'visible_on_front' => 0,
                'global'           => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'apply_to'         => 'appointment'
            ]
        );





        /** assign price attribute to appointment booking product type */
        $fieldList = [
            'price',
            'special_price',
            'special_from_date',
            'special_to_date',
            'minimal_price',
            'cost',
            'tier_price',
        ];

        // make these attributes applicable to booking products
        foreach ($fieldList as $field) {
            $applyTo = explode(
                ',',
                $eavSetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $field, 'apply_to')
            );
            if (!in_array('appointment', $applyTo)) {
                $applyTo[] = 'appointment';
                $eavSetup->updateAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $field,
                    'apply_to',
                    implode(',', $applyTo)
                );
            }
        }
    }
}

