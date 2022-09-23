<?php

declare(strict_types=1);

namespace Ecommerce121\TigTax\Plugin;

use Ecommerce121\TigTax\Model\Service\PostcodeService;
use Ecommerce121\TigTax\Model\StoreConfig;
use Magento\Framework\Serialize\Serializer\Json;
use Ecommerce121\TigTax\Model\Service\RateService;

class RateServiceStub
{
    /**
     * @var StoreConfig
     */
    private $storeConfig;

    /**
     * @var Json
     */
    private $json;

    /**
     * @param StoreConfig $storeConfig
     * @param Json $json
     */
    public function __construct(StoreConfig $storeConfig, Json $json)
    {
        $this->storeConfig = $storeConfig;
        $this->json = $json;
    }

    /**
     * @param RateService $subject
     * @param callable $proceed
     * @param string $zipcode
     * @return array
     */
    // @codingStandardsIgnoreLine
    public function aroundGetRates(RateService $subject, callable $proceed, string $zipcode): array
    {
        if ($this->storeConfig->isStubEnabled()) {
            return $this->getRates($zipcode);
        }

        return $proceed($zipcode);
    }

    /**
     * @param string $zipcode
     * @return array
     */
    private function getRates(string $zipcode): array
    {
        $stubRates = $this->json->unserialize($this->storeConfig->getStubRateResponse());
        $rates = [];

        foreach ($stubRates as $stubRate) {
            $stubZipCode = $stubRate['ZipCode'] ?? '';
            if (!$stubZipCode || ($stubZipCode !== $zipcode)) {
                continue;
            }

            $rates[] = $stubRate;
        }

        return $rates;
    }
}
