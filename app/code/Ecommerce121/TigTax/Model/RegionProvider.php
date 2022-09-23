<?php

declare(strict_types=1);

namespace Ecommerce121\TigTax\Model;

use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;

class RegionProvider
{
    /**
     * @var RegionCollectionFactory
     */
    private $regionCollectionFactory;

    /**
     * @var StoreConfig
     */
    private $storeConfig;

    /**
     * @param RegionCollectionFactory $regionCollectionFactory
     * @param StoreConfig $storeConfig
     */
    public function __construct(
        RegionCollectionFactory $regionCollectionFactory,
        StoreConfig $storeConfig
    ) {
        $this->regionCollectionFactory = $regionCollectionFactory;
        $this->storeConfig = $storeConfig;
    }

    /**
     * @return array
     */
    public function getRegions(): array
    {
        $collection = $this->regionCollectionFactory->create();
        $collection->addFieldToFilter('main_table.region_id', ['in' => $this->storeConfig->getRegionIds()]);
        return $collection->getItems();
    }
}
