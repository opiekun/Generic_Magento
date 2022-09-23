<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://magezon.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_CustomerAttachments
 * @copyright Copyright (C) 2018 Magezon (https://magezon.com)
 */

namespace Magezon\CustomerAttachments\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;


class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'customerattachments_attachment'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('customerattachments_attachment')
        )->addColumn(
            'attachment_id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Attachment ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Attachment Name'
        )->addColumn(
            'description',
            Table::TYPE_TEXT,
            '2M',
            [],
            'Attachment Description'
        )->addColumn(
            'number_of_downloads',
            Table::TYPE_INTEGER,
            10,
            ['nullable' => true],
            'Number of Downloads'
        )->addColumn(
            'attachment_url',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Attachment Url'
        )->addColumn(
            'attachment_file',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Attachment File'
        )->addColumn(
            'attachment_type',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => \Magezon\CustomerAttachments\Model\Attachment::FILE_TYPE_FILE],
            'Attachment Type'
        )->addColumn(
            'attachment_hash',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Attachment Hash'
        )->addColumn(
            'from_date',
            Table::TYPE_DATE,
            null,
            [],
            'From'
        )->addColumn(
            'to_date',
            Table::TYPE_DATE,
            null,
            [],
            'To'
        )->addColumn(
            'conditions_serialized',
            Table::TYPE_TEXT,
            '2M',
            [],
            'Conditions Serialized'
        )->addColumn(
            'actions_serialized',
            Table::TYPE_TEXT,
            '2M',
            [],
            'Actions Serialized'
        )->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Attachment Creation Time'
        )->addColumn(
            'update_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Attachment Modification Time'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is Attachment Active'
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('customerattachments_attachment'),
                ['name', 'description'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['name', 'description'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'customerattachments_attachment_website'
         */
        $table = $installer->getConnection()
        ->newTable(
            $installer->getTable('customerattachments_attachment_website')
        )->addColumn(
            'attachment_id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Attachment ID'
        )->addColumn(
            'website_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Website ID'
        )->addIndex(
            $installer->getIdxName('customerattachments_attachment_website', ['website_id']),
            ['website_id']
        )->addForeignKey(
            $installer->getFkName('customerattachments_attachment_website', 'website_id', 'store_website', 'website_id'),
            'website_id',
            $installer->getTable('store_website'),
            'website_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('customerattachments_attachment_website', 'attachment_id', 'customerattachments_attachment', 'attachment_id'),
            'attachment_id',
            $installer->getTable('customerattachments_attachment'),
            'attachment_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Customer Attachment To Website Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'customerattachments_customer_attachment'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('customerattachments_customer_attachment')
        )->addColumn(
            'attachment_id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Attachment ID'
        )->addColumn(
            'customer_id',
            Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Customer ID'
        )->addColumn(
            'number_of_downloads_used',
            Table::TYPE_INTEGER,
            10,
            ['nullable' => true],
            'Number of downloads used'
        )->addColumn(
            'type',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Customer Attachment Type'
        )->addColumn(
            'position',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Position'
        )->addIndex(
            $installer->getIdxName('customerattachments_customer_attachment', ['customer_id']),
            ['customer_id']
        )->addForeignKey(
            $installer->getFkName('customerattachments_customer_attachment', 'attachment_id', 'customerattachments_attachment', 'attachment_id'),
            'attachment_id',
            $installer->getTable('customerattachments_attachment'),
            'attachment_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('customerattachments_customer_attachment', 'customer_id', 'customer_entity', 'entity_id'),
            'customer_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Customer Attachment'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'customerattachments_customer_attachment_report'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('customerattachments_customer_attachment_report')
        )->addColumn(
            'report_id',
            Table::TYPE_BIGINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Report ID'
        )->addColumn(
            'attachment_id',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Attachment ID'
        )->addColumn(
            'customer_id',
            Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false],
            'Customer ID'
        )->addColumn(
            'website_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Website ID'
        )->addColumn(
            'added_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Added At'
        )->addIndex(
            $installer->getIdxName('customerattachments_customer_attachment_report', ['attachment_id', 'customer_id', 'website_id']),
            ['attachment_id', 'customer_id', 'website_id']
        )->addForeignKey(
            $installer->getFkName('customerattachments_customer_attachment_report', 'attachment_id', 'customerattachments_attachment', 'attachment_id'),
            'attachment_id',
            $installer->getTable('customerattachments_attachment'),
            'attachment_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('customerattachments_customer_attachment_report', 'customer_id', 'customer_entity', 'entity_id'),
            'customer_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('customerattachments_customer_attachment_report', 'website_id', 'store_website', 'website_id'),
            'website_id',
            $installer->getTable('store_website'),
            'website_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Customer Attachment'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'customerattachments_category'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('customerattachments_category')
        )->addColumn(
            'category_id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Category ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Category Name'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'customerattachments_category_attachment'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('customerattachments_category_attachment')
        )->addColumn(
            'category_id',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true],
            'Category ID'
        )->addColumn(
            'attachment_id',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true],
            'Attachment ID'
        )->addColumn(
            'position',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Position'
        )->addIndex(
            $installer->getIdxName('customerattachments_category_attachment', ['attachment_id']),
            ['attachment_id']
        )->addForeignKey(
            $installer->getFkName('customerattachments_category_attachment', 'category_id', 'customerattachments_category', 'category_id'),
            'category_id',
            $installer->getTable('customerattachments_category'),
            'category_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('customerattachments_category_attachment', 'attachment_id', 'customerattachments_attachment', 'attachment_id'),
            'attachment_id',
            $installer->getTable('customerattachments_attachment'),
            'attachment_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Category Attachment'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
