<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Booking
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Booking\Plugin\Adminhtml\Product;

use Magento\Framework\Controller\ResultFactory;
/**
 * Class MassStatus
 * @package Ced\Booking\Plugin\Adminhtml\Product
 */
class MassStatus
{
    /**
     * MassStatus constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Controller\ResultFactory $redirect
     */
    public function __construct(\Magento\Framework\App\RequestInterface $request,
                                ResultFactory $redirect)
    {
        $this->redirect = $redirect;
        $this->request = $request;
    }

    /**
     * @param \Magento\Catalog\Controller\Adminhtml\Product\MassStatus $subject
     */
    public function afterexecute(\Magento\Catalog\Controller\Adminhtml\Product\MassStatus $subject,$result)
    {
        if ($this->request->getParam('booking'))
        {
            $requestStoreId = $storeId = $this->request->getParam('store', null);
            $resultRedirect = $this->redirect->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('booking/product/index', ['store' => $requestStoreId]);
        }
        return $result;
    }
}