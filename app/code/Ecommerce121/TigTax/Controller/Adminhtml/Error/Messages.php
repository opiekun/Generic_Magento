<?php

declare(strict_types=1);

namespace Ecommerce121\TigTax\Controller\Adminhtml\Error;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Messages extends Action implements HttpGetActionInterface
{
    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magento_Backend::system');
        $resultPage->getConfig()->getTitle()->prepend((__('TigTax Error Messages')));

        return $resultPage;
    }
}
