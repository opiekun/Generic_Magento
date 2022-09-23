<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Booking
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Booking\Controller\Adminhtml\Facilities;

use Magento\Backend\App\Action;


/**
 * Class Edit
 * @package Ced\Booking\Controller\Adminhtml\Facilities
 */
class Edit extends \Magento\Backend\App\Action

{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */

    protected $_coreRegistry = null;


    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */

    protected $resultPageFactory;


    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */

    public function __construct(

        Action\Context $context,

        \Magento\Framework\View\Result\PageFactory $resultPageFactory,

        \Magento\Framework\Registry $registry,
        \Ced\Booking\Model\Facilities $facilitiesModel

    )
    {

        $this->resultPageFactory = $resultPageFactory;

        $this->_registry = $registry;

        $this->facilitiesModel = $facilitiesModel;

        parent::__construct($context);

    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */

    protected function _initAction()

    {

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */

        $resultPage = $this->resultPageFactory->create();


        $resultPage->setActiveMenu('Ced_Booking::booking_facilities')
            ->addBreadcrumb(__('Facilities'), __('Facilities'))
            ->addBreadcrumb(__('Manage Facilities'), __('Manage Facilities'));


        return $resultPage;

    }


    /**
     * Edit grid record
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $facilitiesData = $this->facilitiesModel->load($id);
        $this->_registry->register('booking_facilities_data', $facilitiesData);
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()
            ->prepend($facilitiesData->getId() ? $facilitiesData->getTitle() : __('New Facility'));

        return $resultPage;

    }

}