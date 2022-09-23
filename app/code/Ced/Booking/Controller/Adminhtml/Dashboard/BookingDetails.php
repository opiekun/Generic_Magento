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
namespace Ced\Booking\Controller\Adminhtml\Dashboard;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class BookingDetails extends \Magento\Backend\App\Action
{
    /**
     * @param resultPageFactory
     */
    protected $resultPageFactory;

    /**
     * @param resultRedirect
     */
    protected $resultRedirect;

    /**
     * BookingDetails constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(\Magento\Backend\App\Action\Context $context,
                                \Magento\Framework\View\Result\PageFactory $resultPageFactory,
                                JsonFactory $resultJsonFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @param execute
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $order_id = $this->getRequest()->getParam('order_id');
        $order_type = $this->getRequest()->getParam('order_type');
        $template = $resultPage->getLayout()->createBlock('Ced\Booking\Block\Adminhtml\Dashboard\BookingDetails')
            ->setData(['order_id'=>$order_id, 'order_type' => $order_type])
            ->setTemplate('Ced_Booking::dashboard/booking_details.phtml')
            ->toHtml();
        $resultJson =  $this->_resultJsonFactory->create();
        $response = ['template'=>$template];
        return $resultJson->setData($response);

    }
}
