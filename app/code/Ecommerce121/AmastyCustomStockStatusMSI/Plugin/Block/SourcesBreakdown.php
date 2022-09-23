<?php

declare(strict_types=1);

namespace Ecommerce121\AmastyCustomStockStatusMSI\Plugin\Block;

use Amasty\CustomStockStatusMsi\Model\Stockstatus\SourceInformation;
use Amasty\CustomStockStatusMsi\Model\Stockstatus\SourceInformationFactory;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Amasty\CustomStockStatusMsi\Api\Data\SourceStatusInformationInterface;
use Amasty\CustomStockStatusMsi\Block\SourcesBreakdown as AmastySourcesBreakdown;

class SourcesBreakdown
{
    /**
     * @var SourceItemRepositoryInterface
     */
    protected $sourceItems;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var SourceInformationFactory
     */
    private $sourceInformationFactory;


    /**
     * @param SourceInformationFactory $sourceInformationFactory
     * @param SourceItemRepositoryInterface $sourceItems
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        SourceInformationFactory $sourceInformationFactory,
        SourceItemRepositoryInterface $sourceItems,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ){
        $this->sourceItems = $sourceItems;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceInformationFactory = $sourceInformationFactory;
    }

    /**
     * @param AmastySourcesBreakdown $subject
     * @param SourceStatusInformationInterface[] $result
     * @return SourceStatusInformationInterface[]
     */
    public function afterGetSourcesInformation(
        AmastySourcesBreakdown $subject,
        array $result
    ): array
    {
        $sku = $subject->getProduct()->getSku();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('sku', $sku)
            ->create();
        $sourceItemData = $this->sourceItems->getList($searchCriteria);
        foreach ($sourceItemData->getItems() as $sourceItem) {
            $source = $sourceItem->getSourceCode();
            if ($source != 'default') {
                /** @var SourceInformation $sourceInformation */
                $sourceInformation = $this->sourceInformationFactory->create();

                $isInStock = $sourceItem->getStatus() ? __('In Stock') : __('Out of Stock');
                $sourceInformation->setSourceLabel('West Music ' . ucwords($source));
                $sourceInformation->setStatusMessage('' . $isInStock);

                $result[] = $sourceInformation;
            }
        }

        return $result;
    }
}
