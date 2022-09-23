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

namespace Magezon\CustomerAttachments\Block\Account;

class Attachments extends \Magezon\TabGrid\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magezon\CustomerAttachments\Helper\File
     */
    protected $fileHelper;

    /**
     * @var \Magezon\CustomerAttachments\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param \Magezon\TabGrid\Block\Template\Context  $context         
     * @param \Magento\Backend\Helper\Data             $backendHelper   
     * @param \Magento\Customer\Model\Session          $customerSession 
     * @param \Magezon\CustomerAttachments\Helper\File $fileHelper      
     * @param \Magezon\CustomerAttachments\Helper\Data $dataHelper      
     * @param array                                    $data            
     */
    public function __construct(
        \Magezon\TabGrid\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magezon\CustomerAttachments\Helper\File $fileHelper,
        \Magezon\CustomerAttachments\Helper\Data $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->customerSession = $customerSession;
        $this->fileHelper      = $fileHelper;
        $this->dataHelper      = $dataHelper;
    }

    /**
     * @return string
     */
    public function toHtml()
    {	
    	if (!$this->customerSession->isLoggedIn()) {
    		return;
    	}
        $sortBy = $this->dataHelper->getConfig('general/sort_by');

        switch ($sortBy) {
            case 'position':
                $this->setDefaultSort('gposition');
                $this->setDefaultDir('ASC');
                break;
            
            case 'alphabetical':
                $this->setDefaultSort('gname');
                $this->setDefaultDir('ASC');
                break;
        }
    	return parent::toHtml();
    }

    /**
     * Set grid params
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customer-attachments');
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection
     *
     * @return Extended
     */
    protected function _prepareCollection()
    {
        $collection = $this->fileHelper->getCustomerAttachments();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add columns to grid
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'gname',
            [
                'header' => __('Name'),
                'index'  => 'name',
                'renderer' => 'Magezon\CustomerAttachments\Block\Account\Renderer\Attachment\Name'
            ]
        );

        $this->addColumn(
            'gdate',
            [
				'header'           => __('Date'),
				'index'            => 'creation_time',
				'type'             => 'dateTime',
                'header_css_class' => 'a-center',
				'column_css_class' => 'a-center',
				'renderer'         => 'Magezon\CustomerAttachments\Block\Account\Renderer\Attachment\Date'
            ]
        );

        $this->addColumn(
            'gremaining',
            [
				'header'           => __('Remaining Downloads'),
                'header_css_class' => 'a-center',
				'column_css_class' => 'a-center',
				'index'            => 'number_of_downloads_used',
                'sortable'         => false,
                'filter'           => false,
				'renderer'         => 'Magezon\CustomerAttachments\Block\Account\Renderer\Attachment\RemainingDownloads'
            ]
        );

        $this->addColumn(
            'gposition',
            [
				'header' => __('Position'),
				'index'  => 'position',
				'type'   => 'number'
            ]
        );

        $this->addColumn(
            'gdownload',
            [
				'header'           => __('Action'),
                'header_css_class' => 'a-center',
				'column_css_class' => 'a-center',
				'sortable'         => false,
				'filter'           => false,
				'renderer'         => 'Magezon\CustomerAttachments\Block\Account\Renderer\Attachment\Action'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getData(
            'grid_url'
        ) ? $this->getData(
            'grid_url'
        ) : $this->getUrl(
            'mcafiles/*/grid',
            ['_current' => true]
        );
    }

    /**
     * Return row url for js event handlers
     *
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $item
     * @return string
     */
    public function getRowUrl($item)
    {
        return '';
    }
}
