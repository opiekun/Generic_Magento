<?php

declare(strict_types=1);

namespace Ecommerce121\TigTax\Plugin;

use Ecommerce121\TigTax\Model\Service\PostcodeService;
use Ecommerce121\TigTax\Model\StoreConfig;
use Magento\Framework\Serialize\Serializer\Json;

class PostcodeServiceStub
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
     * @param PostcodeService $subject
     * @param callable $proceed
     * @param string $state
     * @return array
     */
    // @codingStandardsIgnoreLine
    public function aroundGetPostcodes(PostcodeService $subject, callable $proceed, string $state): array
    {
        if ($this->storeConfig->isStubEnabled()) {
            return $this->getPostcodes($state);
        }

        return $proceed($state);
    }

    /**
     * @param string $state
     * @return array
     */
    private function getPostcodes(string $state): array
    {
        $stubPostCodes = $this->json->unserialize($this->storeConfig->getStubPostcodeResponse());
        $postcodes = [];

        foreach ($stubPostCodes as $stubPostCode) {
            $stubState = $stubPostCode['State'] ?? '';
            if (!$stubState || ($stubState !== $state)) {
                continue;
            }

            $postcodes[] = $stubPostCode;
        }

        return $postcodes;
    }
}
