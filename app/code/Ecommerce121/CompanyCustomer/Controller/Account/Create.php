<?php

declare(strict_types=1);

namespace Ecommerce121\CompanyCustomer\Controller\Account;

use Magento\Customer\Controller\AccountInterface;
use Magento\Customer\Model\Registration;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Create implements AccountInterface
{
    /**
     * @var Registration
     */
    private $registration;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var RedirectFactory
     */
    private $resultRedirectFactory;

    /**
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     * @param Registration $registration
     * @param RedirectFactory $resultRedirectFactory
     */
    public function __construct(
        Session $customerSession,
        PageFactory $resultPageFactory,
        Registration $registration,
        RedirectFactory $resultRedirectFactory
    ) {
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->registration = $registration;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    /**
     * @return Redirect|Page
     */
    public function execute()
    {
        if ($this->session->isLoggedIn() || !$this->registration->isAllowed()) {
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account');
            return $resultRedirect;
        }

        return $this->resultPageFactory->create();
    }
}
