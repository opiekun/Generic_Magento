<?php

namespace MalibuCommerce\CustomMconnect\Model\Queue\Inventory;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\ObjectManagerInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface;
use Magento\InventoryCatalogApi\Model\SourceItemsProcessorInterface;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;
use MalibuCommerce\MConnect\Model\Config;
use MalibuCommerce\MConnect\Model\Queue\Inventory\SourceItemsProcessor as SourceItemsProcessorSource;

class SourceItemsProcessor extends SourceItemsProcessorSource
{
    protected $config;

    /**
     * @param Config $config
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param ObjectManagerInterface $objectManager
     * @param ProductRepositoryInterface $productRepository
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        Config $config,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        ObjectManagerInterface $objectManager,
        ProductRepositoryInterface $productRepository,
        ProductMetadataInterface $productMetadata
    ) {
        $this->config = $config;

        parent::__construct(
            $searchCriteriaBuilderFactory,
            $objectManager,
            $productRepository,
            $productMetadata
        );
    }

    /**
     * Process inventory source items during inventory sync from NAV to Magento
     *
     * @param ProductInterface $product
     * @param \SimpleXMLElement $data
     * @param int $websiteId
     * @param int $defaultSourceQty
     * @param null|bool $isInStock
     *
     * @return array|string
     *
     * @throws InputException
     */
    public function process($product, \SimpleXMLElement $data, int $websiteId, int $defaultSourceQty, $isInStock = null)
    {
        $this->isSourceItemManagementAllowedForProductType = $this->objectManager->create(
            IsSourceItemManagementAllowedForProductTypeInterface::class
        );
        $this->sourceItemsProcessor = $this->objectManager->create(
            SourceItemsProcessorInterface::class
        );
        $this->defaultSourceProvider = $this->objectManager->create(
            DefaultSourceProviderInterface::class
        );
        $this->sourceItemRepository = $this->objectManager->create(
            SourceItemRepositoryInterface::class
        );

        $sourceItemQty = [];
        if ($this->isSourceItemManagementAllowedForProductType->execute($product->getTypeId()) === false) {
            return sprintf(
                '%s: skipped - is not allowed management of source items for %s product type' . PHP_EOL,
                $product->getSku(),
                $product->getTypeId()
            );
        }

        $sourceCodes = $this->getInventorySourceCodes($websiteId);
        if (empty($sourceCodes)) {
            return sprintf(
                '%s: skipped - inventory sources not found for website id #%s' . PHP_EOL,
                $product->getSku(),
                $websiteId
            );
        }
        if (in_array($this->defaultSourceProvider->getCode(), $sourceCodes)) {
            $sourceItemQty[$this->defaultSourceProvider->getCode()] = $defaultSourceQty;
        }
        foreach ($sourceCodes as $sourceCode) {
            $sourceCodeNode = strtoupper($sourceCode);
            if (isset($data->$sourceCodeNode)) {
                $sourceItemQty[$sourceCode] = (int)$data->$sourceCodeNode;
            }
            $sourceCodeNode = strtolower($sourceCode);
            if (isset($data->$sourceCodeNode)) {
                $sourceItemQty[$sourceCode] = (int)$data->$sourceCodeNode;
            }
        }
        $sourceItems = $this->getSourceItems($product->getSku());
        $newSourceItemsCode = array_diff(
            array_keys($sourceItemQty),
            array_column($sourceItems, SourceItemInterface::SOURCE_CODE)
        );
        foreach ($newSourceItemsCode as $newSourceItemCode) {
            $sourceItems[] = [
                SourceItemInterface::SKU         => $product->getSku(),
                SourceItemInterface::SOURCE_CODE => $newSourceItemCode,
            ];
        }
        foreach ($sourceItems as $key => $sourceItem) {
            if (!isset($sourceItemQty[$sourceItem[SourceItemInterface::SOURCE_CODE]])) {
                continue;
            }
            $qty = (int)$sourceItemQty[$sourceItem[SourceItemInterface::SOURCE_CODE]];
            $sourceItem[SourceItemInterface::QUANTITY] = $qty;
            /** @customization START */
            if (empty($isInStock)) {
                $sourceItems[$key] = $sourceItem;
                continue;
            }
            $sourceItem[SourceItemInterface::STATUS] = in_array(
                $sourceItem[SourceItemInterface::SOURCE_CODE],
                explode(',', $this->config->get('inventory/set_in_stock_for_sources'))
            ) ? 1 : (int)(bool)$qty;
            /** @customization END */
            $sourceItems[$key] = $sourceItem;
        }
        $this->sourceItemsProcessor->execute($product->getSku(), $sourceItems);

        return $sourceItemQty;
    }
}
