<?php

declare(strict_types=1);

namespace Ecommerce121\CompanyCustomer\Plugin\Magento\Customer\Controller\Account\CreatePost;

use Magento\Customer\Controller\Account\CreatePost;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\Message\Error;

class RedirectOnError
{
    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * @param MessageManager $messageManager
     */
    public function __construct(MessageManager $messageManager)
    {
        $this->messageManager = $messageManager;
    }

    /**
     * @param CreatePost $subject
     * @param callable $proceed
     * @return ResultInterface
     */
    public function aroundExecute(CreatePost $subject, callable $proceed): ResultInterface
    {
        $result = $proceed();
        foreach ($this->messageManager->getMessages()->getItems() as $message) {
            if ($message instanceof Error) {
                return $result->setRefererUrl();
            }
        }

        return $result;
    }
}
