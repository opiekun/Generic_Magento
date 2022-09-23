<?php

declare(strict_types=1);

namespace Ecommerce121\TigTax\Model\Resource;

use Magento\Framework\App\ResourceConnection;

class Logger
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
     * @param string $url
     * @param int $code
     * @param string $body
     */
    public function log(string $url, int $code, string $body)
    {
        $this->resourceConnection
            ->getConnection()
            ->insert(
                $this->resourceConnection->getConnection()->getTableName('ecommerce121_tig_tax_logs'),
                [
                    'request' => $url,
                    'code' => $code,
                    'body' => $body,
                ]
            );
    }

    /**
     * @param string $time
     * @return bool
     */
    public function ifLogsExistForTime(string $time): bool
    {
        $sql = $this->resourceConnection
            ->getConnection()
            ->select()
            ->from($this->resourceConnection->getConnection()->getTableName('ecommerce121_tig_tax_logs'))
            ->where('created_at >= ?', $time);

        return (bool) $this->resourceConnection->getConnection()->fetchOne($sql);
    }
}
