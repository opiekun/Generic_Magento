<?php

declare(strict_types=1);

namespace Ecommerce121\TigTax\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class StoreConfig
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var string[]|null
     */
    private $data;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param string[]|null $data
     */
    public function __construct(ScopeConfigInterface $scopeConfig, $data = [])
    {
        $this->scopeConfig = $scopeConfig;
        $this->data = $data;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->getFlag('enable');
    }

    /**
     * @return string
     */
    public function getApiUrl(): string
    {
        return $this->getValue('api_url');
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->getValue('api_key');
    }

    /**
     * @return string
     */
    public function getAppVersion(): string
    {
        return $this->getValue('app_version');
    }

    /**
     * @return string
     */
    public function getAppName(): string
    {
        return $this->getValue('application_name');
    }

    /**
     * @return string
     */
    public function getNameForTaxRule(): string
    {
        return $this->getValue('name_for_tax_rule');
    }

    /**
     * @return array
     */
    public function getRegionIds(): array
    {
        $ids = $this->getValue('region_ids');
        if (!$ids) {
            return [];
        }

        return explode(',', $ids);
    }

    /**
     * @return bool
     */
    public function isStubEnabled(): bool
    {
        return $this->getFlag('stub_enable');
    }

    /**
     * @return string
     */
    public function getStubPostcodeResponse(): string
    {
        return $this->getValue('stub_postcodes');
    }

    /**
     * @return string
     */
    public function getStubRateResponse(): string
    {
        return $this->getValue('stub_rates');
    }

    /**
     * @param string $key
     * @return string
     */
    private function getValue(string $key)
    {
        $xmlConfigPath = $this->data[$key] ?? '';
        if (!$xmlConfigPath) {
            return '';
        }

        return (string) $this->scopeConfig->getValue($xmlConfigPath, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param string $key
     * @return bool
     */
    private function getFlag(string $key): bool
    {
        $xmlConfigPath = $this->data[$key] ?? '';
        if (!$xmlConfigPath) {
            return false;
        }

        return $this->scopeConfig->isSetFlag($xmlConfigPath, ScopeInterface::SCOPE_STORE);
    }
}
