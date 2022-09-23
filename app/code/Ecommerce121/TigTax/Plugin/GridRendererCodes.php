<?php

declare(strict_types=1);

namespace Ecommerce121\TigTax\Plugin;

use Magento\Framework\DataObject;
use Magento\Tax\Block\Grid\Renderer\Codes;
use Magento\Framework\Escaper;

class GridRendererCodes
{
    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @param Escaper $escaper
     */
    public function __construct(Escaper $escaper)
    {
        $this->escaper = $escaper;
    }

    /**
     * @param Codes $subject
     * @param callable $proceed
     * @param DataObject $row
     * @return string
     */
    // @codingStandardsIgnoreLine
    public function aroundRender(Codes $subject, callable $proceed, DataObject $row): string
    {
        $ratesCodes = $row->getTaxRatesCodes();

        if (!is_array($ratesCodes)) {
            return $proceed($row);
        }

        if (count($ratesCodes) > 5) {
            $ratesCodes = array_slice($ratesCodes, 0, 5);
            $ratesCodes[] = '...';
        }

        return $this->escaper->escapeHtml(implode(', ', $ratesCodes));
    }
}
