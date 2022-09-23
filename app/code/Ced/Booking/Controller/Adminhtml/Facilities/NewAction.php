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

/**
 * Class NewAction
 * @package Ced\Booking\Controller\Adminhtml\Facilities
 */
class NewAction extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Ced_Booking::booking_facilities';
    /**

     * @var \Magento\Backend\Model\View\Result\Forward

     */
    protected $resultForwardFactory;


    /**

     * @param \Magento\Backend\App\Action\Context $context

     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory

     */
    public function __construct(

        \Magento\Backend\App\Action\Context $context,

        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory

    ) {

        $this->resultForwardFactory = $resultForwardFactory;

        parent::__construct($context);

    }


    /**
     * @return $this
     */

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();

        return $resultForward->forward('edit');
    }
}