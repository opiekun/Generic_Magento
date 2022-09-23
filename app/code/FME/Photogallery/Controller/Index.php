<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_Photogallery
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\Photogallery\Controller ;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;

abstract class Index extends \Magento\Framework\App\Action\Action
{
    
    protected $session;
    protected $resultPageFactory;
        
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory
    ) {
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function dispatch(RequestInterface $request)
    {
        $enableModule = $this->_objectManager->create('FME\Photogallery\Helper\Data')->enableModule();
        if (!$enableModule) {
            $this->_actionFlag->set('', 'no-dispatch', true);
            $this->messageManager->addError(__('Sorry this feature is not available currently'));
        }
        $result = parent::dispatch($request);
        return $result;
    }
}
