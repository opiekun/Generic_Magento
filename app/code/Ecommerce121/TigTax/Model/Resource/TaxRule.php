<?php

declare(strict_types=1);

namespace Ecommerce121\TigTax\Model\Resource;

use Ecommerce121\TigTax\Model\StoreConfig;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;
use Magento\Tax\Api\TaxRuleRepositoryInterface;
use Magento\Tax\Model\ResourceModel\Calculation\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\Tax\Model\ResourceModel\Calculation\Rate\CollectionFactory as RateCollectionFactory;
use Magento\Tax\Model\TaxClass\Source\Customer as CustomerTaxClassSource;
use Magento\Tax\Model\TaxClass\Source\Product as ProductTaxClassSource;

class TaxRule
{
    /**
     * @var RateCollectionFactory
     */
    private $rateCollectionFactory;

    /**
     * @var RuleCollectionFactory
     */
    private $ruleCollectionFactory;

    /**
     * @var StoreConfig
     */
    private $storeConfig;

    /**
     * @var CustomerTaxClassSource
     */
    private $customerTaxClassSource;

    /**
     * @var ProductTaxClassSource
     */
    private $productTaxClassSource;

    /**
     * @var TaxRuleRepositoryInterface
     */
    private $taxRuleRepository;

    /**
     * @param RateCollectionFactory $rateCollectionFactory
     * @param RuleCollectionFactory $ruleCollectionFactory
     * @param StoreConfig $storeConfig
     * @param CustomerTaxClassSource $customerTaxClassSource
     * @param ProductTaxClassSource $productTaxClassSource
     * @param TaxRuleRepositoryInterface $taxRuleRepository
     */
    public function __construct(
        RateCollectionFactory $rateCollectionFactory,
        RuleCollectionFactory $ruleCollectionFactory,
        StoreConfig $storeConfig,
        CustomerTaxClassSource $customerTaxClassSource,
        ProductTaxClassSource $productTaxClassSource,
        TaxRuleRepositoryInterface $taxRuleRepository
    ) {
        $this->rateCollectionFactory = $rateCollectionFactory;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->storeConfig = $storeConfig;
        $this->customerTaxClassSource = $customerTaxClassSource;
        $this->productTaxClassSource = $productTaxClassSource;
        $this->taxRuleRepository = $taxRuleRepository;
    }

    /**
     * @throws InputException
     * @throws StateException
     */
    public function saveTaxRule()
    {
        $rule = $this->getTaxRule();

        $rule->setTaxRateIds($this->getRateIds());

        if (!$rule->getId()) {
            $rule->setCode($this->storeConfig->getNameForTaxRule());
            $rule->setCustomerTaxClassIds([$this->getCustomerTaxClassId()]);
            $rule->setProductTaxClassIds([$this->getProductTaxClassId()]);
            $rule->setPriority(0);
            $rule->setPosition(0);
        }

        $this->taxRuleRepository->save($rule);
    }

    /**
     * @return DataObject
     */
    private function getTaxRule(): DataObject
    {
        return $this->ruleCollectionFactory->create()
            ->addFieldToFilter('code', ['eq' => $this->storeConfig->getNameForTaxRule()])
            ->setPageSize(1)
            ->setCurPage(1)
            ->getFirstItem();
    }

    /**
     * @return array
     */
    private function getRateIds(): array
    {
        return $this->rateCollectionFactory->create()
            ->addFieldToFilter('code', ['like' => 'tigtax-%'])
            ->getAllIds();
    }

    /**
     * @return string
     * @throws StateException
     */
    private function getCustomerTaxClassId(): string
    {
        return (string) current($this->customerTaxClassSource->getAllOptions())['value'] ?? '';
    }

    /**
     * @return string
     */
    private function getProductTaxClassId(): string
    {
        return (string) current($this->productTaxClassSource->getAllOptions(false))['value'] ?? '';
    }
}
