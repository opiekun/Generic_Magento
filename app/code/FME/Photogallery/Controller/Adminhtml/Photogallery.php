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
namespace FME\Photogallery\Controller\Adminhtml ;

abstract class Photogallery extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    protected $resultLayoutFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        
        return $resultPage;
    }

    protected function _initProductPhotogallery()
    {
        $photogallery = $this->_objectManager->create('FME\Photogallery\Model\Photogallery');
        $photogalleryId  = (int) $this->getRequest()->getParam('id');
        if ($photogalleryId) {
            $photogallery->load($photogalleryId);
        }
        $this->_objectManager->get('Magento\Framework\Registry')
        ->register('current_photogallery_products', $photogallery);
        return $photogallery;
    }
    
    public function splitImageValue($imageValue, $attr = "name")
    {
        $imArray=explode("/", $imageValue);
        $name=$imArray[count($imArray)-1];
        $path=implode("/", array_diff($imArray, [$name]));
        if ($attr=="path") {
            return $path;
        } else { 
            return $name;
        }
    }

    protected function _isAllowed()
    {
        return true;
    }
}
