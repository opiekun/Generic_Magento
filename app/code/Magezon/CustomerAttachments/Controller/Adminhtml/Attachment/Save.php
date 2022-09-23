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
use Magezon\CustomerAttachments\Model\Attachment;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magezon_CustomerAttachments::attachment_save';

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Magezon\CustomerAttachment\Model\AttachmentFactory
     */
    private $attachmentFactory;

    /**
     * @var \Magezon\CustomerAttachment\Api\AttachmentRepositoryInterface
     */
    private $attachmentRepository;

    /**
     * @var \Magento\Catalog\Model\ImageUploader
     */
    private $fileUploader;

    /**
     * @param Action\Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param \Magezon\CustomerAttachments\Model\AttachmentFactory $attachmentFactory
     * @param \Magezon\CustomerAttachments\Api\AttachmentRepositoryInterface $attachmentRepository
     *
     */
    public function __construct(
        Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        DataPersistorInterface $dataPersistor,
        \Magezon\CustomerAttachments\Model\AttachmentFactory $attachmentFactory,
        \Magezon\CustomerAttachments\Api\AttachmentRepositoryInterface $attachmentRepository,
        \Magezon\CustomerAttachments\Helper\File $fileHelper
    ) {
        $this->_logger              = $logger;
        $this->dataPersistor        = $dataPersistor;
        $this->attachmentFactory    = $attachmentFactory;
        $this->attachmentRepository = $attachmentRepository;
        $this->fileHelper           = $fileHelper;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectBack   = $this->getRequest()->getParam('back', false);

        if ($data) {
            if ($redirectBack == 'save_and_send_email') {
                $data['send_email'] = true;
            }
            if (isset($data['is_active']) && $data['is_active'] === 'true') {
                $data['is_active'] = Attachment::STATUS_ENABLED;
            }
            if (empty($data['attachment_id'])) {
                $data['attachment_id'] = null;
            }

            /** @var \Magezon\CustomerAttachments\Model\Attachment $model */
            $model = $this->attachmentFactory->create();

            $id = $this->getRequest()->getParam('attachment_id');
            if ($id) {
                $model = $this->attachmentRepository->getById($id);
                if (!$model->getId()) {
                    $this->messageManager->addErrorMessage(__('This attachment no longer exists.'));
                    /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                    $resultRedirect = $this->resultRedirectFactory->create();
                    return $resultRedirect->setPath('*/*/');
                }
            }

            if (!isset($data['attachment_file'])) {
                $data['attachment_file'] = '';
            }

            if (isset($data['attachment_customers'])
                && is_string($data['attachment_customers'])
            ) {
                $customers = json_decode($data['attachment_customers'], true);
                $data['selected_customers'] = $customers;
                unset($data['attachment_customers']);
            }

            if (isset($data['rule']['conditions'])) {
                $data['conditions'] = $data['rule']['conditions'];
            }
            unset($data['rule']);
            $model->loadPost($data);

            $this->imagePreprocessing($model); 

            $this->_eventManager->dispatch(
                'customerattachments_attachment_prepare_save',
                ['attachment' => $model, 'request' => $this->getRequest()]
            );

            try {
                $this->attachmentRepository->save($model);

                if ($redirectBack == 'save_and_send_email') {
                    $this->messageManager->addSuccessMessage(__('You sent the new attachment email.'));
                }

                if ($this->getRequest()->getParam('save_and_apply')) {
                    $processor = $this->_objectManager->create(\Magezon\CustomerAttachments\Model\Attachment\RuleCustomerProcessor::class);
                    $processor->applyById($model->getId());
                }

                if ($redirectBack === 'save_and_duplicate') {
                    $duplicate = $this->attachmentFactory->create();
                    $duplicate->setData($model->getData());
                    $duplicate->setId(null);
                    $newModel  = $this->attachmentRepository->save($duplicate);
                }

                $this->messageManager->addSuccessMessage(__('You saved the attachment.'));
                $this->dataPersistor->clear('customerattachments_attachment');
                if ($redirectBack === 'save_and_new') {
                    $resultRedirect->setPath(
                        '*/*/new'
                    );
                } elseif ($redirectBack === 'save_and_duplicate') {
                    $resultRedirect->setPath(
                        '*/*/edit',
                        ['attachment_id' => $newModel->getId(), 'back' => null, '_current' => true]
                    );
                } elseif ($redirectBack === 'save_and_close') {
                    $resultRedirect->setPath(
                        '*/*/*'
                    );
                } else {
                    $resultRedirect->setPath(
                        '*/*/edit',
                        ['attachment_id' => $model->getId()]
                    );
                }
                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->_getSession()->setAttachmentData($data);
                $this->messageManager->addExceptionMessage($e->getPrevious() ?:$e);
            } catch (\Exception $e) {
                $this->_getSession()->setAttachmentData($data);
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the attachment.'));
            }

            $this->dataPersistor->set('customerattachments_attachment', $data);
            return $resultRedirect->setPath('*/*/edit', ['attachment_id' => $this->getRequest()->getParam('attachment_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Gets image name from $value array.
     * Will return empty string in a case when $value is not an array
     *
     * @param array $value Attribute value
     * @return string
     */
    private function getUploadedImageName($value)
    {
        if (is_array($value) && isset($value[0]['file'])) {
            return str_replace('.tmp', '', $value[0]['file']);
        }
        return '';
    }

    /**
     * Check if temporary file is available for new image upload.
     *
     * @param array $value
     * @return bool
     */
    private function isTmpFileAvailable($value)
    {
        return is_array($value) && isset($value[0]['file']) && (strpos($value[0]['file'], '.tmp') !== false);
    }

    /**
     * Copy image to public folder
     *
     * @param array $data
     * @return array
     */
    public function imagePreprocessing($model)
    {
        $value = $model->getData('attachment_file');
        if ($fileName = $this->getUploadedImageName($value)) {
            if ($this->isTmpFileAvailable($value)) {
                try {
                    $fileName = $this->fileHelper->moveFileFromTmp($fileName);
                } catch (\Exception $e) {
                    $this->_logger->critical($e);
                }
            }
            $model->setData('attachment_file', $fileName);
        }
        return $model;
    }
}
