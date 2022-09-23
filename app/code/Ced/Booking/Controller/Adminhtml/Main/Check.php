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
namespace Ced\Booking\Controller\Main;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Check extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $resultJsonFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Ced\Booking\Helper\Feed $feedHelper
    )
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->_feedHelper = $feedHelper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        $resultJson = $this->resultJsonFactory->create();
        $data = $this->getRequest()->getParams();
        $json = ['success' => 0, 'module_name' => '', 'module_license' => ''];
        if ($data && isset($data['module_name'])) {
            $json['module_name'] = strtolower($data['module_name']);
            $json['module_license'] = $this->_feedHelper
                ->getStoreConfig(\Ced\Booking\Block\Extensions::HASH_PATH_PREFIX . strtolower($data['module_name']));
            if (strlen($json['module_license']) > 0) $json['success'] = 1;

            $resultJson->setData($json);
            return $resultJson;
        }
           return $this->_forward('noroute');

    }
}