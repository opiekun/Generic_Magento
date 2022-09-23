<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace WeltPixel\OwlCarouselSlider\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the OwlCarouselSlider module DB scheme
 * Add new column to weltpixel_owlcarouselslider_banners table
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.4') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('weltpixel_owlcarouselslider_banners'),
                'custom_css',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => '64k',
                    'nullable' => true,
                    'after'    => 'custom_content',
                    'comment'  => 'Custom CSS'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.5') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('weltpixel_owlcarouselslider_banners'),
                'ga_promo_id',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => '256',
                    'nullable' => false,
                    'default' => '',
                    'comment'  => 'GA Promo ID'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('weltpixel_owlcarouselslider_banners'),
                'ga_promo_name',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => '256',
                    'nullable' => false,
                    'default' => '',
                    'comment'  => 'GA Promo Name'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('weltpixel_owlcarouselslider_banners'),
                'ga_promo_creative',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => '256',
                    'nullable' => false,
                    'default' => '',
                    'comment'  => 'GA Promo Creative'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('weltpixel_owlcarouselslider_banners'),
                'ga_promo_position',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => '256',
                    'nullable' => false,
                    'default' => '',
                    'comment'  => 'GA Promo Position'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.1') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('weltpixel_owlcarouselslider_banners'),
                'mobile_image',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'Mobile Image',
                    'after' => 'image'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.2') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('weltpixel_owlcarouselslider_banners'),
                'wrap_link',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => true,
                    'default' => '0',
                    'comment' => 'Wrap Link',
                    'after' => 'url'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.3') < 0) {
            $setup->getConnection()->modifyColumn(
                $setup->getTable('weltpixel_owlcarouselslider_banners'),
                'slider_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                ]
            );

            $setup->getConnection()->modifyColumn(
                $setup->getTable('weltpixel_owlcarouselslider_banners'),
                'sort_order',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.4') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('weltpixel_owlcarouselslider_banners'),
                'thumb_image',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'after' => 'mobile_image',
                    'comment' => 'Thumb Image'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('weltpixel_owlcarouselslider_sliders'),
                'thumbs',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'unsigned' => true,
                    'default' => 0,
                    'comment' => 'thumbs',
                    'after' => 'dots'
                ]
            );

        }

        if (version_compare($context->getVersion(), '1.1.5') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('weltpixel_owlcarouselslider_sliders'),
                'scheduled_ajax',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'unsigned' => true,
                    'default' => 0,
                    'comment' => 'Ajax Calls required for Scheduled Banners',
                    'after' => 'slider_content'
                ]
            );

        }


        if (version_compare($context->getVersion(), '1.1.6') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('weltpixel_owlcarouselslider_sliders'),
                'dotsEach',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'unsigned' => true,
                    'default' => 0,
                    'comment' => 'dotsEach',
                    'after' => 'dots'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('weltpixel_owlcarouselslider_sliders'),
                'navSpeed',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'unsigned' => true,
                    'default' => 0,
                    'comment' => 'navSpeed',
                    'after' => 'autoHeight'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('weltpixel_owlcarouselslider_sliders'),
                'dotsSpeed',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'unsigned' => true,
                    'default' => 0,
                    'comment' => 'dotsSpeed',
                    'after' => 'autoHeight'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('weltpixel_owlcarouselslider_sliders'),
                'rtl',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'unsigned' => true,
                    'default' => 0,
                    'comment' => 'rtl',
                    'after' => 'autoHeight'
                ]
            );
        }
    }
}
