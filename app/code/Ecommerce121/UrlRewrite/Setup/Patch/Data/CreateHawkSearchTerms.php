<?php

declare(strict_types=1);

namespace Ecommerce121\UrlRewrite\Setup\Patch\Data;

use Magento\Framework\File\Csv;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Search\Model\ResourceModel\Query as SearchResource;

class CreateHawkSearchTerms implements DataPatchInterface
{
    /**
     * @var Csv
     */
    private $csv;

    /**
     * @var Reader
     */
    private $dirReader;

    /**
     * @var SearchResource
     */
    private $searchResource;

    /**
     * @param Csv $csv
     * @param Reader $dirReader
     * @param SearchResource $searchResource
     */
    public function __construct(
        Csv $csv,
        Reader $dirReader,
        SearchResource $searchResource
    ) {
        $this->csv = $csv;
        $this->dirReader = $dirReader;
        $this->searchResource = $searchResource;
    }

    /**
     * @return $this|CreateHawkSearchTerms
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply()
    {
        $this->searchResource->getConnection()->insertMultiple(
            $this->searchResource->getMainTable(),
            $this->getData()
        );

        return $this;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getData(): array
    {
        $filePath = $this->dirReader->getModuleDir('Setup', 'Ecommerce121_UrlRewrite')
            . '/Patch/Fixtures/hawksearch_terms.csv';
        $rows = $this->csv->getData($filePath);
        $keys = \array_shift($rows);

        $data = [];
        foreach ($rows as $row) {
            $data[] = \array_combine($keys, $row);
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }
}
