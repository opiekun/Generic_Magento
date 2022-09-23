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

namespace Magezon\CustomerAttachments\Block\Adminhtml\Attachment\Edit\Tab;

use Magezon\CustomerAttachments\Model\Attachment;

class Customer extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Customer\Api\CustomerMetadataInterface
     */
    protected $customerMetadata;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context                       $context                
     * @param \Magento\Customer\Api\CustomerMetadataInterface               $customerMetadata       
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory 
     * @param \Magento\Backend\Helper\Data                                  $backendHelper          
     * @param \Magento\Customer\Model\CustomerFactory                       $customerFactory        
     * @param array                                                         $data                   
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Api\CustomerMetadataInterface $customerMetadata,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->customerMetadata       = $customerMetadata;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->customerFactory        = $customerFactory;
        $this->coreRegistry           = $registry;
        parent::__construct($context, $backendHelper, $data);
    }

    public function toHtml()
    {
        $this->setId('attachment_customers' . $this->getData('filter_type'));
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        return parent::toHtml();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('customerattachments/*/grid', [
            '_current'    => true,
            'filter_type' => $this->getData('filter_type')
        ]);
    }

    /**
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_attachment') {
            $customerIds = $this->_getSelectedCustomers();
            if (empty($customerIds)) {
                $customerIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $customerIds]);
            } elseif (!empty($customerIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $customerIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $attachmentId = $this->getAttachment()->getId();
        if ($attachmentId) {
            $this->setDefaultFilter(['in_attachment' => 1]);
        }
        $collection = $this->customerFactory->create()->getCollection();
        $collection->addNameToSelect();
        if ($attachmentId) {
            $collection->getSelect()->joinLeft(
                ['cca' => $collection->getResource()->getTable('customerattachments_customer_attachment')],
                'e.entity_id = cca.customer_id',
                ['position']
            )->group('cca.customer_id');
            if ($this->getData('filter_type') == Attachment::TYPE_AUTO) {
                $collection->getSelect()->where(
                    'cca.attachment_id=?', $attachmentId
                )->where(
                    'cca.type=?', Attachment::TYPE_AUTO
                );
            }
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        if ($this->getData('filter_type') == Attachment::TYPE_FIXED) {
            $this->addColumn(
                'in_attachment',
                [
                    'type'             => 'checkbox',
                    'name'             => 'in_attachment',
                    'values'           => $this->_getSelectedCustomers(),
                    'index'            => 'entity_id',
                    'header_css_class' => 'col-select col-massaction',
                    'column_css_class' => 'col-select'
                ]
            );
        }

        $this->addColumn(
            'gentity_id',
            [
                'header'           => __('ID'),
                'align'            => 'left',
                'index'            => 'entity_id',
                'column_css_class' => 'a-center',
                'header_css_class' => 'data-grid-actions-cell'
            ]
        );

        $this->addColumn(
            'gname',
            [
                'header'   => __('Name'),
                'sortable' => true,
                'index'    => 'name'
            ]
        );

        $this->addColumn(
            'gemail',
            [
                'header'   => __('Email'),
                'sortable' => true,
                'index'    => 'email'
            ]
        );

        $this->addColumn(
            'gender',
            [
                'header'           => __('Gender'),
                'sortable'         => true,
                'index'            => 'gender',
                'type'             => 'options',
                'options'          => $this->getGenderOptions(),
                'header_css_class' => 'data-grid-actions-cell'
            ]
        );

        $this->addColumn(
            'group_id',
            [
                'header'           => __('Group'),
                'sortable'         => true,
                'index'            => 'group_id',
                'type'             => 'options',
                'options'          => $this->getCustomerGroups(),
                'header_css_class' => 'data-grid-actions-cell'
            ]
        );

        $this->addColumn(
            'gaction',
            [
                'header'           => __('Action'),
                'filter'           => false,
                'sortable'         => false,
                'column_css_class' => 'a-center',
                'header_css_class' => 'data-grid-actions-cell',
                'renderer'         => 'Magezon\CustomerAttachments\Block\Adminhtml\Renderer\CustomerAction'
            ]
        );

        if ($this->getData('filter_type') == Attachment::TYPE_FIXED) {
            $this->addColumn(
                'position',
                [
                    'header'           => __('Position'),
                    'type'             => 'number',
                    'index'            => 'position',
                    'validate_class'   => 'mgz-position admin__control-text validate-number',
                    'header_css_class' => 'data-grid-actions-cell',
                    'column_css_class' => 'hide-control-value',
                    'editable'         => true
                ]
            );
        }
        return parent::_prepareColumns();
    }

    /**
     * Returns options from gender attribute
     * @return array
     */
    public function getGenderOptions()
    {
        try {
            $genders = [];
            $options = (array) $this->customerMetadata->getAttributeMetadata('gender')->getOptions();
            foreach ($options as $_option) {
                $genders[$_option->getValue()] = $_option->getLabel();
            }
            return $genders;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Get customer groups
     *
     * @return array
     */
    public function getCustomerGroups()
    {
        $collection = $this->groupCollectionFactory->create();
        $options = [];
        foreach ($collection as $group) {
            $options[$group->getId()] = $group->getCustomerGroupCode();
        }
        return $options;
    }

    /**
     * @return array
     */
    protected function _getSelectedCustomers()
    {
        $customers = $this->getRequest()->getPost('selected_customers');
        if ($customers === null) {
            $customers = array_keys($this->getAttachment()->getCustomersPosition($this->getData('filter_type')));
        }
        return $customers;
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function canShowTab()
    {
        return true;
    }

    public function getAttachment()
    {
        return $this->coreRegistry->registry('customerattachments_attachment');
    }
}
