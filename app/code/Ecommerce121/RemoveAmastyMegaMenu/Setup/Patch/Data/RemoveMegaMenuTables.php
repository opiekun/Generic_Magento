<?php

declare(strict_types=1);

namespace Ecommerce121\RemoveAmastyMegaMenu\Setup\Patch\Data;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class RemoveExtraTableS
 */
class RemoveMegaMenuTables implements DataPatchInterface
{
    const MENU_LINK = 'amasty_menu_link';

    const MENU_ITEM_ORDER = 'amasty_menu_item_order';

    const MENU_ITEM_CONTENT = 'amasty_menu_item_content';

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
     * Remove amasty megamenu tables
     *
     * @return void
     */
    public function apply(): void
    {
        $installer = $this->schemaSetup;
        $installer->startSetup();

        if ($installer->tableExists(self::MENU_LINK)) {
            $installer->getConnection()->dropTable($installer->getTable(self::MENU_LINK));
        }

        if ($installer->tableExists(self::MENU_ITEM_ORDER)) {
            $installer->getConnection()->dropTable($installer->getTable(self::MENU_ITEM_ORDER));
        }

        if ($installer->tableExists(self::MENU_ITEM_CONTENT)) {
            $installer->getConnection()->dropTable($installer->getTable(self::MENU_ITEM_CONTENT));
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
