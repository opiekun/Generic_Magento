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

use Magento\Framework\View\Result\PageFactory;

use Magento\Backend\App\Action;

class Index extends Action

{
    const ADMIN_RESOURCE = 'Ced_Booking::booking_facilities';

    /**
     * @var PageFactory
     */

    protected $resultPageFactory;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(

        Action\Context $context,

        \Magento\Framework\View\Result\PageFactory $resultPageFactory

    )
    {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;

    }


    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ced_Booking::booking_facilities');
        $resultPage->addBreadcrumb(__('Add Facilities'), __('Booking Facilities'));
        $resultPage->getConfig()->getTitle()->prepend(__('Booking Facilities'));
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ced_Booking::booking_facilities');
    }

}