<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_NinjaMenus
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\NinjaMenus\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'mgz_ninjamenus_menu'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('mgz_ninjamenus_menu')
        )->addColumn(
            'menu_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Menu ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Name'
        )->addColumn(
            'identifier',
            Table::TYPE_TEXT,
            100,
            ['nullable' => false],
            'Menu Identifier'
        )->addColumn(
            'type',
            Table::TYPE_TEXT,
            100,
            ['nullable' => false],
            'Menu Type'
        )->addColumn(
            'mobile_type',
            Table::TYPE_TEXT,
            100,
            ['nullable' => false],
            'Mobile Type'
        )->addColumn(
            'profile',
            Table::TYPE_TEXT,
            '64M',
            [],
            'Short Code'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            [],
            'Is Menu Active'
        )->addColumn(
            'sticky',
            Table::TYPE_SMALLINT,
            null,
            [],
            'Sticky Menu'
        )->addColumn(
            'mobile_breakpoint',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Mobile Breakpoint'
        )->addColumn(
            'hamburger',
            Table::TYPE_SMALLINT,
            null,
            [],
            'Enable Humberger'
        )->addColumn(
            'hamburger_title',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Hamburger Title'
        )->addColumn(
            'css_classes',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'CSS Classes'
        )->addColumn(
            'custom_css',
            Table::TYPE_TEXT,
            '2M',
            [],
            'Custom CSS'
        )->addColumn(
            'main_color',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Main Color'
        )->addColumn(
            'main_background_color',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Main Background Color'
        )->addColumn(
            'secondary_color',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Secondary Color'
        )->addColumn(
            'secondary_background_color',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Secondary Background Color'
        )->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Menu Creation Time'
        )->addColumn(
            'update_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Menu Modification Time'
        )->setComment(
            'Magezon Menu Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'mgz_ninjamenus_menu_store'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('mgz_ninjamenus_menu_store')
        )->addColumn(
            'menu_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Menu ID'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $installer->getIdxName('mgz_ninjamenus_menu_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('mgz_ninjamenus_menu_store', 'menu_id', 'mgz_ninjamenus_menu', 'menu_id'),
            'menu_id',
            $installer->getTable('mgz_ninjamenus_menu'),
            'menu_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('mgz_ninjamenus_menu_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'NinjaMenus Menu To Store Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        $setup->getConnection()->addColumn(
            $setup->getTable('mgz_ninjamenus_menu'),
            'main_font_size',
            [
                'type'     => Table::TYPE_TEXT,
                'nullable' => true,
                'comment'  => 'Main Font Size',
                'length'   => 255
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('mgz_ninjamenus_menu'),
            'main_font_weight',
            [
                'type'     => Table::TYPE_TEXT,
                'nullable' => true,
                'comment'  => 'Main Font Weight',
                'length'   => 255
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('mgz_ninjamenus_menu'),
            'main_hover_color',
            [
                'type'     => Table::TYPE_TEXT,
                'nullable' => true,
                'comment'  => 'Main Hover Color',
                'length'   => 255
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('mgz_ninjamenus_menu'),
            'main_hover_background_color',
            [
                'type'     => Table::TYPE_TEXT,
                'nullable' => true,
                'comment'  => 'Main Hover Background Color',
                'length'   => 255
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('mgz_ninjamenus_menu'),
            'secondary_hover_color',
            [
                'type'     => Table::TYPE_TEXT,
                'nullable' => true,
                'comment'  => 'Secondary Hover Color',
                'length'   => 255
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('mgz_ninjamenus_menu'),
            'secondary_hover_background_color',
            [
                'type'     => Table::TYPE_TEXT,
                'nullable' => true,
                'comment'  => 'Secondary Hover Background Color',
                'length'   => 255
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('mgz_ninjamenus_menu'),
            'overlay',
            [
                'type'     => Table::TYPE_SMALLINT,
                'nullable' => true,
                'comment'  => 'Overlay'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('mgz_ninjamenus_menu'),
            'overlay_opacity',
            [
                'type'     => Table::TYPE_TEXT,
                'nullable' => true,
                'comment'  => 'Overlay Opacity',
                'length'   => 255
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('mgz_ninjamenus_menu'),
            'hover_delay_timeout',
            [
                'type'     => Table::TYPE_TEXT,
                'nullable' => true,
                'comment'  => 'Hover Delay Timeout',
                'length'   => 255
            ]
        );

        $installer->endSetup();
    }
}