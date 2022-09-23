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
namespace FME\Photogallery\Block;

use Magento\Store\Model\Store;
class Owlcarousal extends \Magento\Framework\View\Element\Template
{
    public $_storeManager;
    public $_scopeConfig;
    public $_helper;
    protected $blockid;
    protected $galleryidentifier;
    protected $gallerytype;
    protected $enablecaption;
    protected $captionposition;
    protected $captionanimation;
    protected $captionalignment;
    protected $captioncolor;
    protected $enableicons;
    protected $enablezoom;
    protected $zoomeffect;
    protected $zoomspeed;
    protected $enablesm;
    protected $smposition;
    protected $smstyle;
    protected $enableicon;
    protected $iconclass;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \FME\Photogallery\Helper\Data $helper,
        \FME\Photogallery\Model\ImgFactory $photogalleryimgFactory,
        \FME\Photogallery\Model\Img $photogalleryimg,
        \FME\Photogallery\Model\PhotogalleryFactory $photogalleryphotogalleryFactory,
        \FME\Photogallery\Model\Photogallery $photogalleryphotogallery,
        \Magento\Framework\App\ResourceConnection $coreresource,
        \FME\Photogallery\Model\ResourceModel\Photogallery\CollectionFactory $blockCollection,
        array $data = []
    ) {
        $this->_blockCollection = $blockCollection;
        $this->_photogalleryimgFactory = $photogalleryimgFactory;
        $this->_photogalleryimg = $photogalleryimg;
        $this->_photogalleryphotogalleryFactory = $photogalleryphotogalleryFactory;
        $this->_photogalleryphotogallery = $photogalleryphotogallery;
        $this->_urlInterface = $context->getUrlBuilder();
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $registry;
        $this->_storeManager = $context->getStoreManager();
        $this->pageConfig = $context->getPageConfig();
        $this->_helper = $helper;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_coreresource = $coreresource;
        parent::__construct($context, $data);
    }
    protected function _tohtml()
    {
     
        $this->blockid = $this->getBlockId();
        $this->galleryidentifier = $this->getGalleryId();
        $this->gallerytype=$this->getGalleryType();
        $this->enablecaption= $this->getEnableCaption();
        $this->captionposition= $this->getCaptionPosition();
        $this->captionanimation= $this->getCaptionAnimation();
        $this->captionalignment= $this->getCaptionAlignment();
        $this->captioncolor= $this->getCaptionColor();
        $this->enableicons= $this->getEnableIcon();
        $this->enablezoom= $this->getEnableZoom();
        $this->zoomeffect= $this->getZoomEffect();
        $this->zoomspeed= $this->getZoomSpeed();
        $this->enablesm= $this->getEnableSm();
        $this->smposition= $this->getSmiconsPosition();
        $this->smstyle= $this->getSmiconsStyle();
        $this->enableicon= $this->getEnableIcon();
        $this->iconclass= $this->getIconClass();
        return parent::_toHtml();
    }
    public function isproductSet()
    {
        return $this->_coreRegistry->registry('product');
    }
    public function isCategorySet()
    {
        return $this->_coreRegistry->registry('current_category')->getId();
    }
    public function getGalleryForCatorPro()
    {
        $product = $this->_coreRegistry->registry('product');
        $storeId = $this->_storeManager->getStore()->getId();
        if ($this->_coreRegistry->registry('product')) {
            $productid = $this->_coreRegistry->registry('product')->getId();
            $galids = array();
            $galleryImages = array();
            $result = $this->getProductGalleries($productid);
            foreach ($result as $r) {
                $galids[] = $r['photogallery_id'];
            }
            if (!empty($galids)) {
                $galleryImages = $this->getProductGimages($galids);
            }
            return  $galleryImages;
        } elseif ($this->_coreRegistry->registry('current_category')->getId()) {
            $cid = $this->_coreRegistry->registry('current_category')->getId();
            $collection = $this->_blockCollection->create()
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->addFieldToFilter(
                'category_ids',
                [
                            ['finset'=> [$cid]]]
            )
            ->addFieldToFilter('main_table.status', 1);
            $galids = array();
            $galleryImages = array();
            $result =  $collection;
            foreach ($result as $r) {
                    $galids[] = $r['photogallery_id'];
            }
            if (!empty($galids)) {
                $galleryImages = $this->getProductGGimages($galids);
            }
            return $galleryImages;
        }
    }
    public function getCategorygallery()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $cid = $this->_coreRegistry->registry('current_category')->getId();
        $collection = $this->_photogalleryphotogalleryFactory->create()
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->addFieldToFilter(
                'category_ids',
                [
                            ['finset'=> [$cid]]]
            )
            ->addFieldToFilter('main_table.status', 1);
        return $collection;
    }
    public function getProductGGimages($photogalleryIds)
    {
        $photogalleryImages = $this->_blockCollection->create()->getPimages($photogalleryIds);
        return $photogalleryImages->getData();
    }
    public function getProduct()
    {
        $product = $this->_coreRegistry->registry('product');
        return $product;
    }
    public function getProductGalleries($productId)
    {
        $store = $this->_storeManager->getStore();
        $pgalleries = $this->_photogalleryphotogalleryFactory->create()
            ->getCollection()
            ->addStoreFilter($store)
            ->getPgalleries($productId);
        return $pgalleries->getData();
    }
    public function getProductGimages($photogalleryIds)
    {
        $photogalleryImages = $this->_photogalleryphotogalleryFactory->create()->getCollection()->getPimages($photogalleryIds);
        return $photogalleryImages->getData();
    }
}
