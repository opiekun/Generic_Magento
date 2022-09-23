<?php

declare(strict_types=1);

namespace Ecommerce121\TigTax\Model;

use Ecommerce121\TigTax\Model\Logger\Logger;
use Ecommerce121\TigTax\Model\Resource\Logger as ResponseErrorLogger;
use Ecommerce121\TigTax\Model\Resource\TaxCalculationRate;
use Ecommerce121\TigTax\Model\Resource\TaxRule;
use Ecommerce121\TigTax\Model\Service\FailedRequestException;
use Ecommerce121\TigTax\Model\Service\PostcodeService;
use Ecommerce121\TigTax\Model\Service\RateService;
use Magento\Directory\Model\Region;
use Magento\Framework\Exception\LocalizedException;

class TigTaxProcessor
{
    /**
     * @var RegionProvider
     */
    private $regionProvider;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var PostcodeService
     */
    private $postcodeService;

    /**
     * @var RateService
     */
    private $rateService;

    /**
     * @var DataProcessor
     */
    private $dataProcessor;

    /**
     * @var TaxCalculationRate
     */
    private $taxCalculationRate;

    /**
     * @var TaxRule
     */
    private $taxRule;

    /**
     * @var LockerFactory
     */
    private $lockerFactory;

    /**
     * @var ResponseErrorLogger
     */
    private $responseErrorLogger;

    /**
     * @param RegionProvider $regionProvider
     * @param Logger $logger
     * @param PostcodeService $postcodeService
     * @param RateService $rateService
     * @param DataProcessor $dataProcessor
     * @param TaxCalculationRate $taxCalculationRate
     * @param TaxRule $taxRule
     * @param LockerFactory $lockerFactory
     * @param ResponseErrorLogger $responseErrorLogger
     */
    public function __construct(
        RegionProvider $regionProvider,
        Logger $logger,
        PostcodeService $postcodeService,
        RateService $rateService,
        DataProcessor $dataProcessor,
        TaxCalculationRate $taxCalculationRate,
        TaxRule $taxRule,
        LockerFactory $lockerFactory,
        ResponseErrorLogger $responseErrorLogger
    ) {
        $this->regionProvider = $regionProvider;
        $this->logger = $logger;
        $this->postcodeService = $postcodeService;
        $this->rateService = $rateService;
        $this->dataProcessor = $dataProcessor;
        $this->taxCalculationRate = $taxCalculationRate;
        $this->taxRule = $taxRule;
        $this->lockerFactory = $lockerFactory;
        $this->responseErrorLogger = $responseErrorLogger;
    }

    /**
     * @return bool
     * @throws LocalizedException
     */
    public function execute(): bool
    {
        $locker = $this->lockerFactory->create();
        if ($locker->isLocked()) {
            return false;
        }

        $locker->lock();

        try {
            $regions = $this->regionProvider->getRegions();
            foreach ($regions as $region) {
                $postcodes = $this->postcodeService->getPostcodes($region->getCode());
                if (!$postcodes) {
                    continue;
                }

                $this->processPostcodes($postcodes, $region);
            }

            $taxCalculationRates = $this->dataProcessor->getTaxCalculationRates();
            if ($taxCalculationRates) {
                $this->taxCalculationRate->import($taxCalculationRates);
                $this->taxRule->saveTaxRule();
            }
        } catch (FailedRequestException $e) {
            throw new LocalizedException(__('TigTax service has failed. See logs for more information.'));
        } catch (\Exception $e) {
            $this->responseErrorLogger->log('', 500, $e->getMessage());
            $this->logger->critical('TigTax service has failed with error: ' . $e->getMessage());
            throw new LocalizedException(__('TigTax service has failed. See logs for more information.'));
        } finally {
            $locker->unlock();
        }

        return true;
    }

    /**
     * @param array $postcodes
     * @param Region $region
     * @throws LocalizedException
     */
    private function processPostcodes(array $postcodes, Region $region)
    {
        foreach ($postcodes as $postcode) {
            if (!isset($postcode['ZipCode'])) {
                continue;
            }

            $rates = $this->rateService->getRates($postcode['ZipCode']);
            foreach ($rates as $rate) {
                if (!isset($rate['SalesTaxRate'])) {
                    continue;
                }

                $this->dataProcessor->addData($region, $postcode, $rate);
            }
        }
    }
}
