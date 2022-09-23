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
 * Class Save
 * @package Ced\Booking\Controller\Adminhtml\Facilities
 */
class Save extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Ced_Booking::booking_facilities';
    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */
    protected $resultRedirectFactory;

    /**
     * @param Magento\Framework\App\Action\Context
     * @param Magento\Backend\Model\View\Result\Redirect
     * @param Magento\Framework\Controller\Result\ForwardFactory
     * @param Magento\Framework\View\Result\PageFactory
     */

    public function __construct(\Magento\Backend\App\Action\Context $context,
                                \Magento\Backend\Model\View\Result\Redirect $resultRedirectFactory,
                                \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
                                \Ced\Booking\Model\Facilities $facilitiesModel)
    {
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->facilitiesModel = $facilitiesModel;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getPost('id');
        $data = $this->getRequest()->getPostValue();
        $imageValue = '';
        if (isset($data['image'][0]['name']) && $data['image_type'] == 'image') {
            $imageValue = $data['image'][0]['name'];
        } elseif ($data['image_type'] == 'icon') {
            $imageValue = $data['icon'];
        }

        if ($id) {
            $this->facilitiesModel->load($id);
        }

        try {
            $this->facilitiesModel->setTitle($data['title'])
                ->setType($data['type'])
                ->setStatus($data['status'])
                ->setImageType($data['image_type'])
                ->setImageValue($imageValue)
                ->save();
            $this->messageManager->addSuccessMessage(__('You have successfully saved facility.'));
            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->facilitiesModel->getId(), '_current' => true]);
            }
        } catch (\Exception $e)
        {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the page.'));
        }
        return $resultRedirect->setPath('*/*/');

    }
}

