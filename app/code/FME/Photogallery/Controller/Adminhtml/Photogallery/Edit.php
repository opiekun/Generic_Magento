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

use \Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
    protected $_coreRegistry = null;

    protected $resultPageFactory;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('FME_Photogallery::manage_items');
    }

    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('FME_Photogallery::manage_items')
            ->addBreadcrumb(__('Photogallery'), __('Photogallery'))
            ->addBreadcrumb(__('Manage Photogallery'), __('Manage Photogallery'));
        return $resultPage;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $id     = $this->getRequest()->getParam('id');
        $model  = $this->_objectManager->create('FME\Photogallery\Model\Photogallery')->load($id);
        $photogallery = $this->_objectManager->create('FME\Photogallery\Model\ImgFactory');
        $collection = $photogallery->create()->getCollection()->addFieldToFilter('photogallery_id', $id);
        if ($model->getId() || $id == 0) {
            $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            $this->_objectManager->get('Magento\Framework\Registry')->register('photogallery_data', $model);
            $this->_objectManager->get('Magento\Framework\Registry')->register('photogallery_img', $collection);
            $resultPage->addBreadcrumb(
                $id ? __('Edit Photogallery') : __('New Photogallery'),
                $id ? __('Edit Photogallery') : __('New Photogallery')
            );
            $resultPage->getConfig()->getTitle()->prepend(__('Photo Gallery'));
            $resultPage->getConfig()->getTitle()
                ->prepend($model->getPhotogalleryId() ? $model->getGalName() : __('New Photo Gallery'));
            return $resultPage;
        } else {
            $this->messageManager->addError(__('File does not exist'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }
    }
}
