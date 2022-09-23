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

use Magento\Framework\Controller\ResultFactory;

class ApplyRules extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magezon_CustomerAttachments::attachment_save';

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
        \Magezon\CustomerAttachments\Model\Attachment\RuleCustomerProcessor $ruleCustomerProcessor
    ) {
        parent::__construct($context);
        $this->ruleCustomerProcessor = $ruleCustomerProcessor;
    }

    /**
     * Forward to edit
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $errorMessage = __('We can\'t apply the rules.');
        try {
            /** @var Job $processor */
            $processor = $this->_objectManager->get(\Magezon\CustomerAttachments\Model\Attachment\RuleCustomerProcessor::class);
            $this->ruleCustomerProcessor->applyAllRules();

            if ($processor->hasSuccess()) {
                $this->messageManager->addSuccess($processor->getSuccess());
            } elseif ($processor->hasError()) {
                $this->messageManager->addError($errorMessage . ' ' . $processor->getError());
            }
        } catch (\Exception $e) {
            $this->_objectManager->create(\Psr\Log\LoggerInterface::class)->critical($e);
            $this->messageManager->addError($errorMessage);
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*');
    }
}
