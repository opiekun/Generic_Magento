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

namespace Magezon\CustomerAttachments\Controller\Adminhtml\Attachment;

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magezon_CustomerAttachments::attachment_save';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry     = $registry;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magezon_CustomerAttachments::attachments')
            ->addBreadcrumb(__('CustomerAttachments'), __('CustomerAttachments'))
            ->addBreadcrumb(__('Manage Attachments'), __('Manage Attachments'));
        return $resultPage;
    }

    /**
     * Edit CustomerAttachments Attachment
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $this->messageManager->addNotice(
            $this->_objectManager->get(\Magento\ImportExport\Helper\Data::class)->getMaxUploadSizeMessage()
        );

        // 1. Get ID and create model
        $id    = $this->getRequest()->getParam('attachment_id');
        $model = $this->_objectManager->create(\Magezon\CustomerAttachments\Model\Attachment::class);

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This attachment no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $attachmentData = $this->_getSession()->getAttachmentData(true);
        if (is_array($attachmentData)) {
            if (isset($attachmentData['selected_customers'])) {
                $selectedCustomers = [];
                foreach ($attachmentData['selected_customers'] as $_id => $_postion) {
                    $selectedCustomers[$_id] = $_id;
                }
                $this->getRequest()->setPostValue('selected_customers', $selectedCustomers);
                $attachmentData['customers_position'] = $attachmentData['selected_customers'];
            }
            $model->addData($attachmentData); 
        }

        $this->_coreRegistry->register('customerattachments_attachment', $model);

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();

        $resultPage->addBreadcrumb(
            $id ? __('Edit Attachment') : __('New Attachment'),
            $id ? __('Edit Attachment') : __('New Attachment')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Attachments'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getName() : __('New Attachment'));

        return $resultPage;
    }
}
