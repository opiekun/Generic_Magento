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
namespace Ced\Booking\Controller\Adminhtml\Order;

class Index extends \Magento\Backend\App\Action
{
    /*** @var \Magento\Framework\View\Result\PageFactory */
    protected $resultPageFactory;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {    parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }


    /**
     * Index action
     * @return void
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ced_Booking::booking_orders');
        $resultPage->addBreadcrumb(__('Manage Booking Orders'), __('Manage Booking Orders'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Booking Orders'));
        return $resultPage;
    }

    protected function _isAllowed()	{
        return $this->_authorization->isAllowed('Ced_Booking::booking_orders');
    }
}