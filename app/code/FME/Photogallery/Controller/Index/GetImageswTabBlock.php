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
namespace FME\Photogallery\Controller\Index;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;

class GetImageswTabBlock extends \FME\Photogallery\Controller\Index
{
    protected $jsonFactory;

    public function __construct(
        Context $context,
        Session $customerSession,
        JsonFactory $jsonFactory,
        PageFactory $resultPageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \FME\Photogallery\Helper\Data $helper,
        \FME\Photogallery\Model\ImgFactory $photogalleryimgFactory,
        \FME\Photogallery\Model\Img $photogalleryimg,
        \Magento\Framework\App\ResourceConnection $coreresource
    ) {
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->_photogalleryimgFactory = $photogalleryimgFactory;
        $this->_photogalleryimg = $photogalleryimg;
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
        $this->jsonFactory = $jsonFactory;
        $this->_coreresource = $coreresource;
        parent::__construct($context, $customerSession, $resultPageFactory);
    }

    public function isLastPage($noOfpage, $pageNumber)
    {
        if ($noOfpage==$pageNumber) {
            return true;
        }
        return false;
    }
    public function isColllectionOver($noOfpage, $pageNumber)
    {
        if ($pageNumber>$noOfpage) {
            return true;
        }
        return false;
    }
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $block = $objectManager->create('FME\Photogallery\Block\Photogalleryblock');
        $string="";
        $store=$this->_storeManager->getStore()->getId();
        $resultJson = $this->jsonFactory->create();
        $_itemsOnPage  =  (int)$this->getRequest()->getParam('peritem');//peritem
        $_currentPage = $this->getRequest()->getParam('page');
        $tab_id = (int)$this->getRequest()->getParam('tabid');
        $id= $this->getRequest()->getParam('catids');//pagination     
        $childs =(int) $this->getRequest()->getParam('child');
        $collection=array();
        $galler="gallery_"; 
        if ($tab_id=="all") {
            $collection=$block->getPhotoGalleryImagesForTabAll($id);
        }
        else
        {
            $collection=$block->getPhotoGalleryTabbyOneId($tab_id);
        }
        $collection->getSelect()->limit($_itemsOnPage, $childs);
        $html1=[];
        $html1= "";
        foreach ($collection as $_gimage) {
            $html1 .= $block->createtilesForTABandScroll($_gimage,"");
        }
        return $resultJson->setData($html1);
    }
}
