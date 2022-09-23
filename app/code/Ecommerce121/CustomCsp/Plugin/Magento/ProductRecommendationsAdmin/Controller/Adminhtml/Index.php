<?php

declare(strict_types=1);

namespace Ecommerce121\CustomCsp\Plugin\Magento\ProductRecommendationsAdmin\Controller\Adminhtml;

use Magento\ProductRecommendationsAdmin\Controller\Adminhtml\Index\Index as MagentoIndex;
use Magento\Framework\View\Result\Page;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;

class Index
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param PageFactory $resultPageFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        PageFactory $resultPageFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param MagentoIndex $subject
     * @param callable $proceed
     * @return Page
     */
    public function aroundExecute(
        MagentoIndex $subject,
        callable $proceed
    ): Page {
        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu('Magento_ProductRecommendationsAdmin::product_recommendations');
        $resultPage->getConfig()->getTitle()->prepend(__('Product Recommendations'));

        $this->setStoreView($subject);

        return $resultPage;
    }

    /**
     * @throws NoSuchEntityException
     */
    private function setStoreView($subject): void
    {
        $params = $subject->getRequest()->getParams();

        $storeId = isset($params['store']) ? $params['store'] : $subject->getRequest()->getParam('store');
        $store = $this->storeManager->getStore($storeId);
        $params['store'] = $store->getId();

        $subject->getRequest()->setParams($params);
    }
}
