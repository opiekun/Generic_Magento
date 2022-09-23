<?php

declare(strict_types=1);

namespace Ecommerce121\TigTax\Model;

use Magento\Directory\Model\Region;

class DataProcessor
{
    /**
     * @var RangeManager
     */
    private $rangeManager;

    /**
     * @var array
     */
    private $rateStorage = [];

    /**
     * @var array
     */
    private $postcodeStorage = [];

    /**
     * @param RangeManager $rangeManager
     */
    public function __construct(
        RangeManager $rangeManager
    ) {
        $this->rangeManager = $rangeManager;
    }

    /**
     * @param Region $region
     * @param array $postcodeData
     * @param array $rateData
     */
    public function addData(Region $region, array $postcodeData, array $rateData)
    {
        $key = 'US-' . $region->getCode() . '-' . $rateData['SalesTaxRate'];

        $this->rateStorage[$key . '-' . $postcodeData['ZipCode']] = [
            'tax_country_id' => 'US',
            'tax_region_id' => $region->getRegionId(),
            'rate' => $rateData['SalesTaxRate']
        ];

        $this->postcodeStorage[$key][(int) $postcodeData['ZipCode']] = (int) $postcodeData['ZipCode'];
    }

    /**
     * @see table tax_calculation_rate
     *
     * @return array
     */
    public function getTaxCalculationRates()
    {
        $taxCalculationRates = [];

        foreach ($this->postcodeStorage as $key => $postcodes) {
            $ranges = $this->rangeManager->getRanges($postcodes);

            foreach ($ranges as $range) {
                [$from, $to] = $this->formatValues($range);
                $data = $this->rateStorage[$key . '-' . $from];

                if ($from === $to) {
                    $data['code'] = 'tigtax-' . $key . '-' . $from;
                    $data['tax_postcode'] = $from;
                    $data['zip_is_range'] = null;
                    $data['zip_from'] = null;
                    $data['zip_to'] = null;
                } else {
                    $data['code'] = 'tigtax-' . $key . '-' . $range;
                    $data['tax_postcode'] = $from . '-' . $to;
                    $data['zip_is_range'] = 1;
                    $data['zip_from'] = $from;
                    $data['zip_to'] = $to;
                }

                $taxCalculationRates[] = $data;
            }
        }

        return $taxCalculationRates;
    }

    /**
     * @param $range
     * @return array
     */
    private function formatValues($range): array
    {
        [$from, $to] = explode('-', $range);
        $from = str_pad($from, 5, "0", STR_PAD_LEFT);
        $to = str_pad($to, 5, "0", STR_PAD_LEFT);
        return [$from, $to];
    }
}
