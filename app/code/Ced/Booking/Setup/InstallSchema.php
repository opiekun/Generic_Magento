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
 * @category  Ced
 * @package   Ced_booking
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license   https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Booking\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'ced_booking_facilities'
         */
        $facilitiesTable = $installer->getTable ( 'ced_booking_facilities' );
        if ($installer->getConnection ()->isTableExists ( $facilitiesTable ) != true) {
            $tableFacilities = $installer->getConnection ()->newTable ( $facilitiesTable )
                ->addColumn ( 'id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ], 'Id'
                )->addColumn(
                    'title',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Facility Title'
                )->addColumn(
                    'type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Facility Type'
                )->addColumn(
                    'image_type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '255',
                    ['nullable' => true],
                    'Facility Image Type'
                )->addColumn(
                    'image_value',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '255',
                    ['nullable' => true],
                    'Facility Image/Icon Value'
                )->addColumn(
                    'status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'null',
                    ['nullable' => true],
                    'Status'
                )->setComment ( 'book room Table' )->setOption ( 'type', 'InnoDB' )->setOption ( 'charset', 'utf8' );
            $installer->getConnection ()->createTable ( $tableFacilities );
        }


        /*ced_booking_rent_order (appointment & rental daily, hourly)*/
        $rentorder = $installer->getTable ( 'ced_booking_rent_order' );
        if ($installer->getConnection ()->isTableExists ( $rentorder ) != true) {
            $tablerentorder = $installer->getConnection ()->newTable ( $rentorder )->addColumn ( 'id', Table::TYPE_INTEGER, null, [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ], 'Id' )->addColumn ( 'order_id', Table::TYPE_TEXT, 255, [
                'nullable' => false
            ], 'Order Id' )->addColumn ( 'product_id', Table::TYPE_INTEGER, null, [
                'nullable' => false,
                'default' => 0
            ], 'Product Id' )->addColumn ( 'start_date', Table::TYPE_DATETIME, null, [
                'nullable' => false
            ], 'Booking Start Date' )->addColumn ( 'end_date', Table::TYPE_DATETIME, null, [
                'nullable' => false
            ], 'Booking End Date' )->addColumn ( 'total_days', Table::TYPE_INTEGER, null, [
                'nullable' => false,
                'default' => 0
            ], 'Total Days' )->addColumn ( 'total_hours', Table::TYPE_TEXT, 255, [
                'nullable' => false
            ], 'Total Hours' )->addColumn ( 'qty_ordered', Table::TYPE_INTEGER, null, [
                'nullable' => false,
                'default' => 0
            ], 'Qty Ordered' )->addColumn ( 'qty_invoiced', Table::TYPE_INTEGER, null, [
                'nullable' => false,
                'default' => 0
            ], 'Qty Invoiced' )->addColumn ( 'qty_refunded', Table::TYPE_INTEGER, null, [
                'nullable' => false,
                'default' => 0
            ], 'Qty Refunded' )->addColumn ( 'product_type', Table::TYPE_TEXT, 255, [
                'nullable' => false
            ], 'Product Type' )->addColumn ( 'status', Table::TYPE_TEXT, 255, [
                'nullable' => false
            ], 'Status' )->setComment ( 'book room Table' )->setOption ( 'type', 'InnoDB' )->setOption ( 'charset', 'utf8' );
            $installer->getConnection ()->createTable ( $tablerentorder );
        }

    }
}
