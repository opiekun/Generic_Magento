<?php

declare(strict_types=1);

namespace Ecommerce121\RemovePreOrderData\Setup\Patch\Data;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class RemoveExtraTable
 */
class RemovePreOrderTables implements DataPatchInterface
{
    const ORDER = 'amasty_preorder_order_preorder';

    const ORDER_ITEM = 'amasty_preorder_order_item_preorder';

    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    public function __construct(
        SchemaSetupInterface $schemaSetup
    ) {
        $this->schemaSetup = $schemaSetup;
    }

    /**
     * Remove amasty preorder tables
     *
     * @return void
     */
    public function apply(): void
    {
        $installer = $this->schemaSetup;
        $installer->startSetup();

        if ($installer->tableExists(self::ORDER)) {
            $installer->getConnection()->dropTable($installer->getTable(self::ORDER));
        }

        if ($installer->tableExists(self::ORDER_ITEM)) {
            $installer->getConnection()->dropTable($installer->getTable(self::ORDER_ITEM));
        }

        $installer->endSetup();
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
