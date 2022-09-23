<?php

declare(strict_types=1);

namespace Ecommerce121\SkuRoute\Controller;

use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;

class Router implements RouterInterface
{

    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * @var ProductFactory
     */
    private $productFactory;


    /**
     * @param ActionFactory $actionFactory
     * @param ProductFactory $productFactory
     */
    public function __construct(
        ActionFactory $actionFactory,
        ProductFactory $productFactory
    ) {
        $this->actionFactory = $actionFactory;
        $this->productFactory = $productFactory;
    }

    /**
     * @param RequestInterface $request
     * @return false|ActionInterface
     */
    public function match(RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        $productId = $this->getIdBySku($identifier);
        if ($productId) {
            $this->setRequestParam($request, $productId);
            return $this->actionFactory->create(
                Forward::class,
                ['request' => $request]
            );
        }
        return false;
    }

    /**
     * Initialize Request Param
     *
     * @param RequestInterface $request
     * @param integer $productId
     * @return void
     */
    private function setRequestParam($request, $productId)
    {
        $request->setModuleName('catalog')
                ->setControllerName('product')
                ->setActionName('view')
                ->setParam('id', $productId);
    }

    /**
     * @param $sku
     * @return int
     */
    private function getIdBySku($sku): int
    {
        return (int)$this->productFactory->create()->getIdBySku($sku);
    }
}
