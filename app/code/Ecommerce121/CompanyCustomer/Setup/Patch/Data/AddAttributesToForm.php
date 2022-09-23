<?php

declare(strict_types=1);

namespace Ecommerce121\CompanyCustomer\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\App\ResourceConnection;

class AddAttributesToForm implements DataPatchInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return AddAttributesToForm
     */
    public function apply(): AddAttributesToForm
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($connection->getTableName('customer_form_attribute'), 'attribute_id')
            ->where('form_code = ?', 'customer_account_create');

        $attributeIds = $connection->fetchCol($select);
        $data = [];
        foreach ($attributeIds as $attributeId) {
            $data[] = [
                'form_code' => 'company_customer_account_create',
                'attribute_id' => $attributeId,
            ];
        }

        $connection->insertMultiple($connection->getTableName('customer_form_attribute'), $data);

        return $this;
    }

    /**
     * @return string[]
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }
}
