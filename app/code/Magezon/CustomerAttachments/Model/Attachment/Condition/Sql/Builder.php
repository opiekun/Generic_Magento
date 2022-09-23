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

namespace Magezon\CustomerAttachments\Model\Attachment\Condition\Sql;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Combine;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;


class Builder
{
    /**
     * @var array
     */
    protected $_conditionOperatorMap = [
        '=='    => ':field = ?',
        '!='    => ':field <> ?',
        '>='    => ':field >= ?',
        '>'     => ':field > ?',
        '<='    => ':field <= ?',
        '<'     => ':field < ?',
        '{}'    => ':field IN (?)',
        '!{}'   => ':field NOT IN (?)',
        '()'    => ':field IN (?)',
        '!()'   => ':field NOT IN (?)',
        'like'   => ':field LIKE (?)'
    ];

    /**
     * @var \Magento\Rule\Model\Condition\Sql\ExpressionFactory
     */
    protected $_expressionFactory;

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @param ExpressionFactory $expressionFactory
     * @param AttributeRepositoryInterface|null $attributeRepository
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Sql\ExpressionFactory $expressionFactory,
        AttributeRepositoryInterface $attributeRepository = null
    ) {
        $this->_expressionFactory = $expressionFactory;
        $this->attributeRepository = $attributeRepository ?:
            ObjectManager::getInstance()->get(AttributeRepositoryInterface::class);
    }

    /**
     * Returns sql expression based on rule condition.
     *
     * @param AbstractCondition $condition
     * @param string $value
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getMappedSqlCondition(AbstractCondition $condition, $value = '')
    {
        $argument = $condition->getMappedSqlField();

        // If rule hasn't valid argument - create negative expression to prevent incorrect rule behavior.
        if (empty($argument)) {
            return $this->_expressionFactory->create(['expression' => '1 = -1']);
        }

        $conditionOperator = $condition->getOperatorForValidate();

        if (!isset($this->_conditionOperatorMap[$conditionOperator])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Unknown condition operator'));
        }

        $fieldValue = $condition->getBindArgumentValue();
        if ($argument == 'orders_sum' || $argument == 'orders_number') {
        	$fieldValue = (int)$fieldValue;
        }
        $sql = str_replace(
			':field',
			$this->_connection->getIfNullSql($this->_connection->quoteIdentifier($argument), 0),
			$this->_conditionOperatorMap[$conditionOperator]
        );

        if ($argument == 'is_subscriber') {
        	$fieldValue = (int)$fieldValue;
        	if ($fieldValue == 0) {
        		$sql = str_replace('=', '!=', $sql);
        	}
        	$fieldValue = 3;
        }

        if ($conditionOperator == 'like') {
            $fieldValue = '%' . $fieldValue . '%';
        } 
	    return $this->_expressionFactory->create(
            ['expression' => $value . $this->_connection->quoteInto($sql, $fieldValue)]
        );
    }

    /**
     * @param Combine $combine
     * @param string $value
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getMappedSqlCombination(Combine $combine, $value = '')
    {
        $out = (!empty($value) ? $value : '');
        $value = ($combine->getValue() ? '' : ' NOT ');
        $getAggregator = $combine->getAggregator();
        $conditions = $combine->getConditions();
        foreach ($conditions as $key => $condition) {
            /** @var $condition AbstractCondition|Combine */
            $con = ($getAggregator == 'any' ? Select::SQL_OR : Select::SQL_AND);
            $con = (isset($conditions[$key+1]) ? $con : '');
            if ($condition instanceof Combine) {
                $out .= $this->_getMappedSqlCombination($condition, $value);
            } else {
                $out .= $this->_getMappedSqlCondition($condition, $value);
            }
            $out .=  $out ? (' ' . $con) : '';
        }
        return $this->_expressionFactory->create(['expression' => $out]);
    }


    /**
     * Attach conditions filter to collection
     *
     * @param AbstractCollection $collection
     * @param Combine $combine
     *
     * @return void
     */
    public function attachConditionToCollection(
        AbstractCollection $collection,
        Combine $combine
    ) {
        $this->_connection = $collection->getResource()->getConnection();
        $this->_joinTablesToCollection($collection, $combine);
        $whereExpression = (string)$this->_getMappedSqlCombination($combine);

        return $whereExpression;
        if (!empty($whereExpression)) {
            // Select ::where method adds braces even on empty expression
            $collection->getSelect()->having($whereExpression);
        }
    }

    /**
     * Join tables from conditions combination to collection
     *
     * @param AbstractCollection $collection
     * @param Combine $combine
     * @return $this
     */
    protected function _joinTablesToCollection(
        AbstractCollection $collection,
        Combine $combine
    ) {
        foreach ($this->_getCombineTablesToJoin($combine) as $alias => $joinTable) {
            /** @var $condition AbstractCondition */
            $collection->getSelect()->joinLeft(
                [$alias => $collection->getResource()->getTable($joinTable['name'])],
                $joinTable['condition'],
                isset($joinTable['columns']) ? $joinTable['columns'] : '*'
            );
        }
        return $this;
    }

    /**
     * Get tables to join for given conditions combination
     *
     * @param Combine $combine
     * @return array
     */
    protected function _getCombineTablesToJoin(Combine $combine)
    {
        $tables = $this->_getChildCombineTablesToJoin($combine);
        return $tables;
    }

    /**
     * Get child for given conditions combination
     *
     * @param Combine $combine
     * @param array $tables
     * @return array
     */
    protected function _getChildCombineTablesToJoin(Combine $combine, $tables = [])
    {
        foreach ($combine->getConditions() as $condition) {
            if ($condition->getConditions()) {
                $tables = $this->_getChildCombineTablesToJoin($condition);
            } else {
                /** @var $condition AbstractCondition */
                foreach ($condition->getTablesToJoin() as $alias => $table) {
                    if (!isset($tables[$alias])) {
                        $tables[$alias] = $table;
                    }
                }
            }
        }
        return $tables;
    }
}
