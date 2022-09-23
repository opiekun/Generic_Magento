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

namespace Magezon\CustomerAttachments\Api\Data;

interface ConditionInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const TYPE            = 'type';
    
    const ATTRIBUTE       = 'attribute';
    
    const OPERATOR        = 'operator';
    
    const VALUE           = 'value';
    
    const IS_VALUE_PARSED = 'is_value_parsed';
    
    const AGGREGATOR      = 'aggregator';
    
    const CONDITIONS      = 'conditions';
    /**#@-*/

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $attribute
     * @return $this
     */
    public function setAttribute($attribute);

    /**
     * @return string
     */
    public function getAttribute();

    /**
     * @param string $operator
     * @return $this
     */
    public function setOperator($operator);

    /**
     * @return string
     */
    public function getOperator();

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value);

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param bool $isValueParsed
     * @return $this
     */
    public function setIsValueParsed($isValueParsed);

    /**
     * @return bool|null
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsValueParsed();

    /**
     * @param string $aggregator
     * @return $this
     */
    public function setAggregator($aggregator);

    /**
     * @return string
     */
    public function getAggregator();

    /**
     * @param \Magezon\CustomerAttachments\Api\Data\ConditionInterface[] $conditions
     * @return $this
     */
    public function setConditions($conditions);

    /**
     * @return \Magezon\CustomerAttachments\Api\Data\ConditionInterface[]|null
     */
    public function getConditions();

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\CatalogRule\Api\Data\ConditionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magento\CatalogRule\Api\Data\ConditionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magento\CatalogRule\Api\Data\ConditionExtensionInterface $extensionAttributes
    );
}
