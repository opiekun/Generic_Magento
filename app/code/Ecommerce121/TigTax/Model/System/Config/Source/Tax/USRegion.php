<?php

declare(strict_types=1);

namespace Ecommerce121\TigTax\Model\System\Config\Source\Tax;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Tax\Model\System\Config\Source\Tax\Region as RegionSource;

class USRegion implements OptionSourceInterface
{
    private const US_CODE = 'US';

    /**
     * @var RegionSource
     */
    private $regionSource;

    /**
     * @param RegionSource $regionSource
     */
    public function __construct(RegionSource $regionSource)
    {
        $this->regionSource = $regionSource;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return $this->regionSource->toOptionArray(true, self::US_CODE);
    }
}
