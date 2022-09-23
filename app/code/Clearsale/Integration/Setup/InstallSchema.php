<?php

namespace Clearsale\Integration\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.0', '<')) {
                $table = $setup->getConnection()->newTable(
                        $setup->getTable('clearsale_order_diagnostic')
                )->addColumn(
                        'order_id',
                        Table::TYPE_TEXT,
                        20,
                        ['identity' => false, 'nullable' => false, 'primary' => true],
                        'Order ID'
                )->addColumn(
                        'clearsale_status',
                        Table::TYPE_TEXT,
                        50,
                        ['nullable' => true],
                        'Status'
                )->addColumn(
                        'score',
                        Table::TYPE_TEXT,
                        5,
                        ['nullable' => true],
                        'Score'
                )->addColumn(
                        'diagnostics',
                        Table::TYPE_TEXT,
                        255,
                        ['nullable' => true],
                        'Diagnostics'
                )->addColumn(
                        'dt_sent',
                        Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => true],
                        'Sent Date'
                )->addColumn(
                        'dt_update',
                        Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => true],
                        'Update Date'
                );
                $setup->getConnection()->createTable($table);


                $table = $setup->getConnection()->newTable(
                        $setup->getTable('clearsale_order_control')
                )->addColumn(
                        'order_id',
                        Table::TYPE_TEXT,
                        20,
                        ['identity' => false, 'nullable' => false, 'primary' => true],
                        'Order ID'
                )->addColumn(
                        'diagnostics',
                        Table::TYPE_TEXT,
                        255,
                        ['nullable' => true],
                        'Diagnostics'
                )->addColumn(
                        'attempts',
                        Table::TYPE_INTEGER,
                         null,
                         ['nullable' => true ],
                        'Attempts'
                )->addColumn(
                        'dt_sent',
                        Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => true, 'default' => '0'],
                        'Sent Date'
                )->addColumn(
                        'dt_update',
                        Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => true, 'default' => '0'],
                        'Update Date'
                );
                $setup->getConnection()->createTable($table);
        }
		
        $setup->endSetup();
    }
}
