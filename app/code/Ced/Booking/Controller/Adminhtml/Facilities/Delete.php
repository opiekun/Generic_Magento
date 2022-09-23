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

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Delete
 * @package Ced\Booking\Controller\Adminhtml\Facilities
 */
class Delete extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Ced_Booking::booking_facilities';

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ced\Booking\Model\Facilities $facilitiesModel
    ) {
        $this->facilitiesModel = $facilitiesModel;
        parent::__construct($context);
    }
    /**
     * Delete customer action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $isPost = $this->getRequest()->isPost();
        $facilityId = (int)$this->getRequest()->getParam('id');
        if (!empty($facilityId)) {
            try {
                $this->facilitiesModel->load($facilityId)->delete();
                $this->messageManager->addSuccessMessage(__('You deleted facility.'));
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/facilities/index');
    }
}
