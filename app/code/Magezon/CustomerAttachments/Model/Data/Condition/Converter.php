<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://magezon.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_CustomerAttachments
 * @copyright Copyright (C) 2018 Magezon (https://magezon.com)
 */

namespace Magezon\CustomerAttachments\Model\Data\Condition;

class Converter
{
    /**
     * @var \Magezon\CustomerAttachments\Api\Data\ConditionInterfaceFactory
     */
    protected $ruleConditionFactory;

    /**
     * @param \Magezon\CustomerAttachments\Api\Data\ConditionInterfaceFactory $ruleConditionFactory
     */
    public function __construct(\Magezon\CustomerAttachments\Api\Data\ConditionInterfaceFactory $ruleConditionFactory)
    {
        $this->ruleConditionFactory = $ruleConditionFactory;
    }

    /**
     * @param \Magento\CatalogRule\Api\Data\ConditionInterface $dataModel
     * @return array
     */
    public function dataModelToArray(\Magezon\CustomerAttachments\Api\Data\ConditionInterface $dataModel)
    {
        $conditionArray = [
            'type'               => $dataModel->getType(),
            'attribute'          => $dataModel->getAttribute(),
            'operator'           => $dataModel->getOperator(),
            'value'              => $dataModel->getValue(),
            'is_value_processed' => $dataModel->getIsValueParsed(),
            'aggregator'         => $dataModel->getAggregator()
        ];

        foreach ((array)$dataModel->getConditions() as $condition) {
            $conditionArray['conditions'][] = $this->dataModelToArray($condition);
        }

        return $conditionArray;
    }

    /**
     * @param array $conditionArray
     * @return \Magento\CatalogRule\Api\Data\ConditionInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function arrayToDataModel(array $conditionArray)
    {
        /** @var \Magento\CatalogRule\Api\Data\ConditionInterface $ruleCondition */
        $ruleCondition = $this->ruleConditionFactory->create();

        $ruleCondition->setType($conditionArray['type']);
        $ruleCondition->setAggregator(isset($conditionArray['aggregator']) ? $conditionArray['aggregator'] : false);
        $ruleCondition->setAttribute(isset($conditionArray['attribute']) ? $conditionArray['attribute'] : false);
        $ruleCondition->setOperator(isset($conditionArray['operator']) ? $conditionArray['operator'] : false);
        $ruleCondition->setValue(isset($conditionArray['value']) ? $conditionArray['value'] : false);
        $ruleCondition->setIsValueParsed(
            isset($conditionArray['is_value_parsed']) ? $conditionArray['is_value_parsed'] : false
        );

        if (isset($conditionArray['conditions']) && is_array($conditionArray['conditions'])) {
            $conditions = [];
            foreach ($conditionArray['conditions'] as $condition) {
                $conditions[] = $this->arrayToDataModel($condition);
            }
            $ruleCondition->setConditions($conditions);
        }
        return $ruleCondition;
    }
}
