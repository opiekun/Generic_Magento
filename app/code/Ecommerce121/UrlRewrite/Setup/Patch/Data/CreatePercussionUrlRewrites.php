<?php

declare(strict_types=1);

namespace Ecommerce121\UrlRewrite\Setup\Patch\Data;

use Magento\Framework\File\Csv;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewrite as UrlRewriteResource;

class CreatePercussionUrlRewrites implements DataPatchInterface
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
     * @var UrlRewriteResource
     */
    private $urlRewriteResource;

    /**
     * @param Csv $csv
     * @param Reader $dirReader
     * @param UrlRewriteResource $urlRewriteResource
     */
    public function __construct(
        Csv $csv,
        Reader $dirReader,
        UrlRewriteResource $urlRewriteResource
    ) {
        $this->csv = $csv;
        $this->dirReader = $dirReader;
        $this->urlRewriteResource = $urlRewriteResource;
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function apply()
    {
        $this->urlRewriteResource->getConnection()->insertMultiple(
            $this->urlRewriteResource->getMainTable(),
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
            . '/Patch/Fixtures/percussionsource_redirects.csv';
        $rows = $this->csv->getData($filePath);
        $keys = \array_shift($rows);

        $data = [];
        foreach ($rows as $row) {
            $data[] = \array_combine($keys, $row);
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }
}
