<?php

declare(strict_types=1);

namespace Ecommerce121\TigTax\Model\Resource;

use Magento\Framework\App\ResourceConnection;

class TaxCalculationRate
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
     * @param array $data
     * @throws \Zend_Db_Exception
     */
    public function import(array $data)
    {
        $this->skipCheckingConstraints();
        $this->cleanTempTable();
        $this->copyValuesFromTheMainTableIntoTempTable();
        $this->deleteAllTigTaxValues();
        $this->importData($data);
        $this->switchTables();
        $this->fixConstraints();
    }

    /**
     * @return void
     */
    private function skipCheckingConstraints()
    {
        $this->resourceConnection->getConnection()->query('SET FOREIGN_KEY_CHECKS=0;');
    }

    /**
     * @return void
     */
    private function cleanTempTable()
    {
        $this->resourceConnection
            ->getConnection()
            ->truncateTable(
                $this->resourceConnection->getTableName('tax_calculation_rate_tmp')
            );
    }

    /**
     * @return void
     */
    private function copyValuesFromTheMainTableIntoTempTable()
    {
        $select = $this->resourceConnection
            ->getConnection()
            ->select()
            ->from($this->resourceConnection->getTableName('tax_calculation_rate'));

        $sql = $this->resourceConnection
            ->getConnection()
            ->insertFromSelect(
                $select,
                $this->resourceConnection->getTableName('tax_calculation_rate_tmp')
            );

        $this->resourceConnection->getConnection()->query($sql);
    }

    /**
     * @return void
     */
    private function deleteAllTigTaxValues()
    {
        $this->resourceConnection
            ->getConnection()
            ->delete(
                $this->resourceConnection->getTableName('tax_calculation_rate_tmp'),
                'code LIKE "tigtax-%"'
            );
    }

    /**
     * @return void
     */
    private function importData(array $data)
    {
        $this->resourceConnection
            ->getConnection()
            ->insertMultiple(
                $this->resourceConnection->getTableName('tax_calculation_rate_tmp'),
                $data
            );
    }

    /**
     * @return void
     * @throws \Zend_Db_Exception
     */
    private function switchTables()
    {
        $tableName = $this->resourceConnection->getTableName('tax_calculation_rate');

        $renameBatch = [
            [
                'oldName' => $tableName,
                'newName' => $tableName . '_outdated',
            ],
            [
                'oldName' => $tableName . '_tmp',
                'newName' => $tableName
            ],
            [
                'oldName' => $tableName . '_outdated',
                'newName' => $tableName . '_tmp'
            ]
        ];

        $this->resourceConnection
            ->getConnection()
            ->renameTablesBatch($renameBatch);
    }

    /**
     * @return void
     */
    private function fixConstraints()
    {
        $this->resourceConnection->getConnection()
            ->dropForeignKey(
                'tax_calculation_rate_title',
                'FK_37FB965F786AD5897BB3AE90470C42AB'
            );

        $this->resourceConnection->getConnection()
            ->addForeignKey(
                'FK_37FB965F786AD5897BB3AE90470C42AB',
                'tax_calculation_rate_title',
                'tax_calculation_rate_id',
                'tax_calculation_rate',
                'tax_calculation_rate_id',
                'CASCADE'
            );

        $this->resourceConnection->getConnection()->dropForeignKey(
            'tax_calculation',
            'TAX_CALC_TAX_CALC_RATE_ID_TAX_CALC_RATE_TAX_CALC_RATE_ID'
        );

        $this->resourceConnection->getConnection()->addForeignKey(
            'TAX_CALC_TAX_CALC_RATE_ID_TAX_CALC_RATE_TAX_CALC_RATE_ID',
            'tax_calculation',
            'tax_calculation_rate_id',
            'tax_calculation_rate',
            'tax_calculation_rate_id',
            'CASCADE'
        );
    }
}
