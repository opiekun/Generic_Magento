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
namespace FME\Photogallery\Controller\Adminhtml\Photogallery;

class Index extends \FME\Photogallery\Controller\Adminhtml\Photogallery
{
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
         $resultPage->setActiveMenu('FME_Photogallery::manage_items');
        $resultPage->addBreadcrumb(__('Photogallery'), __('Photo Gallery'));
        $resultPage->addBreadcrumb(__('Photo Gallery'), __('Manage Photogallery'));
        $resultPage->getConfig()->getTitle()->prepend(__('Photo Gallery'));
        return $resultPage;
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('FME_Photogallery::manage_items');
    }
}
