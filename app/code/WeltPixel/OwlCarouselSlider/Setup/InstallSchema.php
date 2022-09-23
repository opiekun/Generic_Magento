<?php

namespace WeltPixel\OwlCarouselSlider\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Install schema
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for module
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        // Get weltpixel_owlcarouselslider_banners table
        $tableName = $installer->getTable('weltpixel_owlcarouselslider_banners');

        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {

            /*
             * Create table weltpixel_owlcarouselslider_banners
             */

            $table = $installer->getConnection()->newTable(
                $tableName
            )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Banner Id'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'Banner Status'
            )->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Banner Title'
            )->addColumn(
                'show_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => true, 'default' => '0'],
                'Show Banner Title'
            )->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                ['nullable' => true, 'default' => ''],
                'Banner Description'
            )->addColumn(
                'show_description',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => true, 'default' => '0'],
                'Show Banner Description'
            )->addColumn(
                'banner_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => true, 'default' => '0'],
                'Banner Type'
            )->addColumn(
                'slider_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Slider Id'
            )->addColumn(
                'url',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true, 'default' => ''],
                'Banner Url'
            )
            ->addColumn(
                'wrap_link',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => true, 'default' => '0'],
                'Wrap Link'
            )->addColumn(
                'target',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => true, 'default' => '_blank'],
                'Banner Url Target'
            )->addColumn(
                'video',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                ['nullable' => true, 'default' => ''],
                'Banner Video'
            )->addColumn(
                'image',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Banner Image'
            )->addColumn(
                'custom',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                ['nullable' => true, 'default' => ''],
                'Banner Custom HTML'
            )->addColumn(
                'alt_text',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Banner Image Alt Text'
            )->addColumn(
                'button_text',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Button Text'
            )->addColumn(
                'custom_content',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                ['nullable' => true],
                'Custom Content'
            )->addColumn(
                'valid_from',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Banner Valid From'
            )->addColumn(
                'valid_to',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Banner Valid To'
            )->addColumn(
                'sort_order',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Banner Sort Ordert'
            )->addIndex(
                $installer->getIdxName('weltpixel_owlcarouselslider_banners', ['id']),
                ['id']
            )->addIndex(
                $installer->getIdxName('weltpixel_owlcarouselslider_banners', ['status']),
                ['status']
            )->addIndex(
                $installer->getIdxName('weltpixel_owlcarouselslider_banners', ['slider_id']),
                ['slider_id']
            )->addIndex(
                $installer->getIdxName('weltpixel_owlcarouselslider_banners', ['valid_from']),
                ['valid_from']
            )->addIndex(
                $installer->getIdxName('weltpixel_owlcarouselslider_banners', ['valid_to']),
                ['valid_to']
            );

            $installer->getConnection()->createTable($table);
        }

        /*
         * Create table weltpixel_owlcarouselslider_sliders
         */

        // Get weltpixel_owlcarouselslider_sliders table
        $tableName = $installer->getTable('weltpixel_owlcarouselslider_sliders');

        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            $table = $installer->getConnection()->newTable(
                $tableName
            )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => true, 'default' => '0'],
                'Slider Status'
            )->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'Custom Slider'],
                'Slider Title'
            )->addColumn(
                'show_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => true, 'default' => '0'],
                'Show Title'
            )->addColumn(
                'slider_content',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                ['nullable' => true, 'default' => ''],
                'Slider Content'
            )->addColumn(
                'nav',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => true, 'default' => '1'],
                'Navigation'
            )->addColumn(
                'dots',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => true, 'default' => '1'],
                'Dots'
            )->addColumn(
                'center',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => true, 'default' => '1'],
                'Center'
            )->addColumn(
                'items',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'default' => '1'],
                'Items'
            )->addColumn(
                'loop',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => true, 'default' => '1'],
                'Loop'
            )->addColumn(
                'margin',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'default' => '0'],
                'Margin'
            )->addColumn(
                'stagePadding',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'default' => '0'],
                'StagePadding'
            )->addColumn(
                'lazyLoad',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => true, 'default' => '1'],
                'LazyLoad'
            )->addColumn(
                'transition',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true, 'default' => 'fadeOut'],
                'Transition'
            )->addColumn(
                'autoplay',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => true, 'default' => '1'],
                'Autoplay'
            )->addColumn(
                'autoplayTimeout',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'default' => '3000'],
                'AutoplayTimeout'
            )->addColumn(
                'autoplayHoverPause',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => true, 'default' => '1'],
                'AutoplayHoverPause'
            )->addColumn(
                'autoHeight',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => true, 'default' => '1'],
                'AutoHeight'
            )->addColumn(
                'nav_brk1',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => true, 'default' => '1'],
                'Breakpoint 1 Nav'
            )->addColumn(
                'items_brk1',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'default' => '1'],
                'Breakpoint 1 Items'
            )->addColumn(
                'nav_brk2',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => true, 'default' => '1'],
                'Breakpoint 2 Nav'
            )->addColumn(
                'items_brk2',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'default' => '1'],
                'Breakpoint 2 Items'
            )->addColumn(
                'nav_brk3',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => true, 'default' => '1'],
                'Breakpoint 3 Nav'
            )->addColumn(
                'items_brk3',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'default' => '1'],
                'Breakpoint 3 Items'
            )->addColumn(
                'nav_brk4',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => true, 'default' => '1'],
                'Breakpoint 4 Nav'
            )->addColumn(
                'items_brk4',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'default' => '1'],
                'Breakpoint 4 Items'
            )->addIndex(
                $installer->getIdxName('weltpixel_owlcarouselslider_sliders', ['id']),
                ['status']
            )->addIndex(
                $installer->getIdxName('weltpixel_owlcarouselslider_sliders', ['status']),
                ['status']
            );

            $installer->getConnection()->createTable($table);
        }
        
        $installer->endSetup();
    }
}
