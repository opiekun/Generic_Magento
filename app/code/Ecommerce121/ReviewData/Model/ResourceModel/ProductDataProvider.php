<?php

declare(strict_types=1);

namespace Ecommerce121\ReviewData\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;

class ProductDataProvider
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var array
     */
    private $productMapping;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param string $sku
     * @return int|null
     */
    public function getProductIdBySku(string $sku): ?int
    {
        if (null === $this->productMapping) {
            $sql = $this->resourceConnection
                ->getConnection()
                ->select()
                ->from($this->resourceConnection->getTableName('catalog_product_entity'), ['sku', 'entity_id']);

            $this->productMapping = $this->resourceConnection
                ->getConnection()
                ->fetchPairs($sql);
        }

        if (!isset($this->productMapping[$sku])) {
            return null;
        }

        return (int) $this->productMapping[$sku];
    }
}
