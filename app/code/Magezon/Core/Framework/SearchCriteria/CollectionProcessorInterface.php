<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_Core
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Core\Framework\SearchCriteria;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\AbstractDb;

interface CollectionProcessorInterface
{
    /**
     * Apply Search Criteria to Collection
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param AbstractDb $collection
     * @throws \InvalidArgumentException
     * @return void
     * @since 100.2.0
     */
    public function process(SearchCriteriaInterface $searchCriteria, AbstractDb $collection);
}
