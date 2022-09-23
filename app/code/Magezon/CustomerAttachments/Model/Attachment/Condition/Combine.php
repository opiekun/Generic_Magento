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

namespace Magezon\CustomerAttachments\Model\Attachment\Condition;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var \Magezon\CustomerAttachments\Model\Attachment\Condition\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @param \Magento\Rule\Model\Condition\Context                                   $context          
     * @param \Magento\Customer\Model\ResourceModel\Customer                          $customerResource 
     * @param \Magezon\CustomerAttachments\Model\Attachment\Condition\CustomerFactory $conditionFactory 
     * @param array                                                                   $data             
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Customer\Model\ResourceModel\Customer $customerResource,
        \Magezon\CustomerAttachments\Model\Attachment\Condition\CustomerFactory $conditionFactory,
        array $data = []
    ) {
        $this->_customerFactory = $conditionFactory;
        parent::__construct($context, $data);
        $this->setType(\Magezon\CustomerAttachments\Model\Attachment\Condition\Combine::class);
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $customerAttributes = $this->_customerFactory->create()->loadAttributeOptions()->getAttributeOption();
        $attributes         = [];
        foreach ($customerAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'Magezon\CustomerAttachments\Model\Attachment\Condition\Customer|' . $code,
                'label' => $label
            ];
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => \Magezon\CustomerAttachments\Model\Attachment\Condition\Combine::class,
                    'label' => __('Conditions Combination')
                ],
                [
                    'label' => __('Customer'),
                    'value' => $attributes
                ]
            ]
        );

        return $conditions;
    }

    /**
     * @param array $customerCollection
     * @return $this
     */
    public function collectValidatedAttributes($customerCollection)
    {
        foreach ($this->getConditions() as $condition) {
            /** @var Customer|Combine $condition */
            $condition->collectValidatedAttributes($customerCollection);
        }
        return $this;
    }
}
