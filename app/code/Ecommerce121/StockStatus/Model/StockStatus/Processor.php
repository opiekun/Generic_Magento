<?php

declare(strict_types=1);

namespace Ecommerce121\StockStatus\Model\StockStatus;

use Amasty\Stockstatus\Api\Data\StockstatusInformationInterface;
use Amasty\Stockstatus\Api\Data\StockstatusInformationInterfaceFactory;
use Amasty\Stockstatus\Api\StockstatusSettings\GetByOptionIdAndStoreIdInterface;
use Amasty\Stockstatus\Api\StockstatusSettings\GetIconUrlByStockstatusSettingInterface;
use Amasty\Stockstatus\Model\Stockstatus\Formatter;
use Amasty\Stockstatus\Model\Stockstatus\Processor as AmastyProcessor;
use Amasty\Stockstatus\Model\Stockstatus\Specification\SpecificationInterface;
use Magento\Catalog\Api\Data\ProductInterface;

class Processor extends AmastyProcessor
{
    /**
     * @var StockstatusInformationInterfaceFactory
     */
    private $stockstatusInformationFactory;

    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @var SpecificationInterface[]
     */
    private $specifications;

    /**
     * @var GetByOptionIdAndStoreIdInterface
     */
    private $getAdditionalStockstatusSettings;

    /**
     * @var GetIconUrlByStockstatusSettingInterface
     */
    private $getIconUrlByStockstatusSetting;

    /**
     * @param StockstatusInformationInterfaceFactory $stockstatusInformationFactory
     * @param Formatter $formatter
     * @param GetByOptionIdAndStoreIdInterface $getAdditionalStockstatusSettings
     * @param GetIconUrlByStockstatusSettingInterface $getIconUrlByStockstatusSetting
     * @param array $specifications
     */
    public function __construct(
        StockstatusInformationInterfaceFactory $stockstatusInformationFactory,
        Formatter $formatter,
        GetByOptionIdAndStoreIdInterface $getAdditionalStockstatusSettings,
        GetIconUrlByStockstatusSettingInterface $getIconUrlByStockstatusSetting,
        array $specifications)
    {
        parent::__construct($stockstatusInformationFactory, $formatter, $getAdditionalStockstatusSettings, $getIconUrlByStockstatusSetting, $specifications);
        $this->stockstatusInformationFactory = $stockstatusInformationFactory;
        $this->formatter = $formatter;
        $this->getAdditionalStockstatusSettings = $getAdditionalStockstatusSettings;
        $this->getIconUrlByStockstatusSetting = $getIconUrlByStockstatusSetting;
        $this->setSpecifications($specifications);
    }

    /**
     * Sort specifications by sort_order and save sorted objects.
     *
     * @param array $specifications
     * @return void
     */
    protected function setSpecifications(array $specifications): void
    {
        usort($specifications, function (array $specificationLeft, array $specificationRight) {
            if ($specificationLeft['sort_order'] == $specificationRight['sort_order']) {
                return 0;
            }
            return ($specificationLeft['sort_order'] < $specificationRight['sort_order']) ? -1 : 1;
        });

        $this->specifications = array_column($specifications, 'object');
    }

    /**
     * Try resolve stock status for product
     * and in success case populate extension attributes with stockstatus information
     *
     * @param ProductInterface[] $products
     * @return void
     */
    public function execute(array $products): void
    {
        foreach ($products as $product) {
            if ($product->getExtensionAttributes()->getStockstatusInformation()) {
                continue;
            }

            $this->initExtensionAttributes($product);

            $statusId = null;
            foreach ($this->specifications as $specification) {
                if ($statusId = $specification->resolve($product)) {
                    $this->populateExtensionAttributes($product, $statusId);
                    break;
                }
            }
        }
    }

    /**
     * @param ProductInterface $product
     */
    private function initExtensionAttributes(ProductInterface $product): void
    {
        /** @var StockstatusInformationInterface $stockstatusInformation */
        $stockstatusInformation = $this->stockstatusInformationFactory->create();
        $product->getExtensionAttributes()->setStockstatusInformation($stockstatusInformation);
    }

    /**
     * @param ProductInterface $product
     * @param int|null $statusId
     */
    private function populateExtensionAttributes(ProductInterface $product, ?int $statusId): void
    {
        $stockstatusInformation = $product->getExtensionAttributes()->getStockstatusInformation();
        $stockstatusInformation->setStatusId($statusId);
        $stockstatusInformation->setStatusMessage(
            $this->formatter->execute($product, $statusId)
        );

        if (null !== $statusId) {
            $stockstatusSettings = $this->getAdditionalStockstatusSettings->execute($statusId, $product->getStoreId());
            $stockstatusInformation->setStatusIcon(
                $this->getIconUrlByStockstatusSetting->execute($stockstatusSettings)
            );
            $stockstatusInformation->setTooltipText($stockstatusSettings->getTooltipText());
            $stockstatusInformation->setAdditionalContent($stockstatusSettings->getAdditionalContent());
        }
    }
}
