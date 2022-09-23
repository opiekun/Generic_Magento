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

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magezon\CustomerAttachments\Api\Data\AttachmentInterface;

class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magezon_CustomerAttachments::attachment_save';

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error      = false;
        $messages   = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $attachmentId) {
            /** @var \Magezon\CustomerAttachments\Model\Attachment $attachment */
            $attachment = $this->_objectManager->create(\Magezon\CustomerAttachments\Model\Attachment::class);
            try {
                $attachment->load($attachmentId);
                $attachmentData         = $postItems[$attachmentId];
                $extendedAttachmentData = $attachment->getData();
                $this->setAttachmentData($attachment, $extendedAttachmentData, $attachmentData); 
                $attachment->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithRowId($attachment, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithRowId($attachment, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithRowId(
                    $attachment,
                    __('Something went wrong while saving the attachment.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * @param $attachment
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithRowId($attachment, $errorText)
    {
        return '[Attachment ID: ' . $attachment->getId() . '] ' . $errorText;
    }

    /**
     * Set row data
     *
     * @param $attachment
     * @param array $extendedAttachmentData
     * @param array $attachmentData
     * @return $this
     */
    public function setAttachmentData($attachment, array $extendedAttachmentData, array $attachmentData)
    {
        $attachment->setData(array_merge($attachment->getData(), $extendedAttachmentData, $attachmentData));
        return $this;
    }
}
