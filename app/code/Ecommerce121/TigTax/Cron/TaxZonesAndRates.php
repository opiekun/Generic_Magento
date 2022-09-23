<?php

declare(strict_types=1);

namespace Ecommerce121\TigTax\Cron;

use Ecommerce121\TigTax\Model\StoreConfig;
use Magento\Framework\Exception\LocalizedException;
use Ecommerce121\TigTax\Model\TigTaxProcessor;

class TaxZonesAndRates
{
    /**
     * @var StoreConfig
     */
    private $storeConfig;

    /**
     * @var TigTaxProcessor
     */
    private $tigTaxProcessor;

    /**
     * @param StoreConfig $storeConfig
     * @param TigTaxProcessor $tigTaxProcessor
     */
    public function __construct(StoreConfig $storeConfig, TigTaxProcessor $tigTaxProcessor)
    {
        $this->storeConfig = $storeConfig;
        $this->tigTaxProcessor = $tigTaxProcessor;
    }

    /**
     * @throws LocalizedException
     */
    public function execute()
    {
        if ($this->storeConfig->isEnabled()) {
            $this->tigTaxProcessor->execute();
        }
    }
}
