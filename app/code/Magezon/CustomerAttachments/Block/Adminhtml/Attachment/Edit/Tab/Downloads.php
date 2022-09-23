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

class Downloads extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Backend\Block\Template\Context $context         
     * @param \Magento\Backend\Helper\Data            $backendHelper   
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory 
     * @param \Magento\Framework\Registry             $registry        
     * @param array                                   $data            
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
		$this->customerFactory = $customerFactory;
		$this->registry        = $registry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('downloads_report');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/downloadsReport', ['_current' => true]);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
		$attachmentId = $this->getAttachment()->getId();
		$collection   = $this->customerFactory->create()->getCollection();
		$collection->addNameToSelect();

		$collection->getSelect()->joinLeft(
            ['ccar' => $collection->getResource()->getTable('customerattachments_customer_attachment_report')],
            'e.entity_id = ccar.customer_id',
            ['downloads' => 'COUNT(ccar.customer_id)']
        )->where(
        	'ccar.attachment_id=?', $attachmentId
        )->group('e.entity_id');

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
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
            'gdownloads',
            [
                'header'   => __('Downloads'),
                'sortable' => true,
                'index'    => 'downloads'
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * Retrieve current attachment
     *
     * @return \Magezon\CustomerAttachments\Model\Attachment
     */
    public function getAttachment()
    {
        return $this->registry->registry('customerattachments_attachment');
    }
}
