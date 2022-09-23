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

namespace Magezon\CustomerAttachments\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;

class Router implements RouterInterface
{
    /**
     * @var bool
     */
    protected $dispatched;

    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @var \Magezon\ShopByBrand\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param ActionFactory                            $actionFactory 
     * @param \Magezon\CustomerAttachments\Helper\Data $dataHelper    
     */
    public function __construct(
        ActionFactory $actionFactory,
        \Magezon\CustomerAttachments\Helper\Data $dataHelper
    ) {
        $this->actionFactory = $actionFactory;
        $this->dataHelper    = $dataHelper;
    }

    public function match(RequestInterface $request)
    {
        if (!$this->dispatched && $this->dataHelper->getRoute() && $this->dataHelper->isEnable()) {
            $route    = $this->dataHelper->getConfig('general/route');
            $pathInfo = trim($request->getPathInfo(), '/');
            $paths    = explode("/", $pathInfo);

            if (count($paths) > 1 && ($paths[0] == $route) && ($paths[1] == 'file')) {
                $request->setModuleName('mcafiles')
                    ->setControllerName('file')
                    ->setActionName('download');

                $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $pathInfo);
                $request->setDispatched(true);
                $this->dispatched = true;
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward',
                    ['request' => $request]
                );
            }

            if (count($paths) >= 1 && ($paths[0] == $route)) {
                if (count($paths)==1) {
                        $request->setModuleName('mcafiles')
                        ->setControllerName('index')
                        ->setActionName('index');
                    } else {
                        $request->setModuleName('mcafiles')
                        ->setControllerName($paths[1])
                        ->setActionName((isset($paths[2]) ? $paths[2] : 'index' ));
                    }

                $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $pathInfo);
                $request->setDispatched(true);
                $this->dispatched = true;
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward',
                    ['request' => $request]
                );
            }
        }
    }
}
