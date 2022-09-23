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

class PList extends \Magento\Framework\View\Element\Template
{
    public $_storeManager;
    public $_scopeConfig;
    public $_helper;
    protected $_pagesCount = null;
    protected $_currentPage = null;
    protected $_itemsOnPage;
    protected $_itemsLimit;
    protected $_pages;
    protected $_displayPages   = 10;

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
        array $data = []
    ) {
     
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
        $itemsPerPage = $this->_helper->getPagination();
        if ($itemsPerPage > 0) {
            $this->_itemsOnPage = $itemsPerPage;
        } else {
            $this->_itemsOnPage = 10;
        }
        parent::__construct($context, $data);
    }

    public function _prepareLayout()
    {
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $metaKeywords = $this->_helper->getMetaKeywords();
        $metaDescription = $this->_helper->getMetaDescription();
        $this->pageConfig->setKeywords($metaKeywords);
        $this->pageConfig->setDescription($metaDescription);
        $breadcrumbs->addCrumb(
            'home',
            [
            'label' => __('Home'),
            'title' => __('Go to Home Page'),
            'link' => $this->_storeManager->getStore()->getBaseUrl()]
        );
        $breadcrumbs->addCrumb(
            'photogallery',
            [
            'label' => __('Photogallery'),
            'title' => __('Photogallery'),
            'link' => false,
            ]
        );
        return parent::_prepareLayout();
    }

    public function getAllPhotoGalleryImages()
    {
        $store=$this->_storeManager->getStore()->getId();
        $collection = $this->_photogalleryimgFactory->create()->getCollection();
        $collection->getSelect()->join(
            ['pht_item'=> $this->_coreresource->getTableName('photogallery')],
            'main_table.photogallery_id = pht_item.photogallery_id'
        )->join(
            ['store_table' => $this->_coreresource->getTableName(
                'photogallery_store'
            )
            ],
            'main_table.photogallery_id = store_table.photogallery_id',
            []
        )->where(' store_table.store_id in (?)', [0, $store]);
        $collection->getSelect()->where('status = 1');
        $collection->getSelect()->where('show_in in (1,3)')->distinct(true)->order('main_table.img_order ASC');
        return $collection;
    }
    public function getPhotoGalleryImagesbyId($id)
    {
        $store=$this->_storeManager->getStore()->getId();
        $collection = $this->_photogalleryimgFactory->create()->getCollection();
        $collection->getSelect()->join(
            ['pht_item'=> $this->_coreresource->getTableName('photogallery')],
            'main_table.photogallery_id = pht_item.photogallery_id'
        )->join(
            ['store_table' => $this->_coreresource->getTableName(
                'photogallery_store'
            )
            ],
            'main_table.photogallery_id = store_table.photogallery_id',
            []
        )->where(' store_table.store_id in (?)', [0, $store]);
        $collection->getSelect()->where('status = 1');
        $collection->getSelect()->where('show_in in (1,3)')->order('main_table.img_order ASC');
        $collection->getSelect()->where('main_table.photogallery_id ='.$id)->distinct(true);
        return $collection;
    }

    public function getotherPhotogallery($id)
    {
        $this->_currentPage = $this->getRequest()->getParam('page');
        if (!$this->_currentPage) {
            $this->_currentPage=1;
        }
        $collection = $this->getPhotoGalleryImagesbyId($id);
        if (!$this->_helper->enablepaginationonFrontend()) {
            $collection->getSelect()->where('status = 1');
            return $collection;
        }
        if ($this->_itemsLimit!=null && $this->_itemsLimit<$collection->getSize()) {
            $this->_pagesCount = ceil($this->_itemsLimit/$this->_itemsOnPage);
        } else {
            $this->_pagesCount = ceil($collection->getSize()/$this->_itemsOnPage);
        }
        for ($i=1; $i<=$this->_pagesCount; $i++) {
            $this->_pages[] = $i;
        }
        $this->setLastPageNum($this->_pagesCount);
        $offset = $this->_itemsOnPage*($this->_currentPage-1);
        if ($this->_itemsLimit!=null) {
            $_itemsCurrentPage = $this->_itemsLimit - $offset;
            if ($_itemsCurrentPage > $this->_itemsOnPage) {
                $_itemsCurrentPage = $this->_itemsOnPage;
            }
            $collection->getSelect()->limit($_itemsCurrentPage, $offset);
        } else {
            $collection->getSelect()->limit($this->_itemsOnPage, $offset);
        }
        $collection->getSelect()->where('status = 1');
        return $collection;
    } 
    public function getAllPhotogallery()
    {
        $this->_currentPage = $this->getRequest()->getParam('page');
        if (!$this->_currentPage) {
            $this->_currentPage=1;
        }
        $collection = $this->getAllPhotoGalleryImages();
        if ($this->_itemsLimit!=null && $this->_itemsLimit<$collection->getSize()) {
            $this->_pagesCount = ceil($this->_itemsLimit/$this->_itemsOnPage);
        } else {
            $this->_pagesCount = ceil($collection->getSize()/$this->_itemsOnPage);
        }
        for ($i=1; $i<=$this->_pagesCount; $i++) {
            $this->_pages[] = $i;
        }
        $this->setLastPageNum($this->_pagesCount);
        $offset = $this->_itemsOnPage*($this->_currentPage-1);
        if ($this->_itemsLimit!=null) {
            $_itemsCurrentPage = $this->_itemsLimit - $offset;
            if ($_itemsCurrentPage > $this->_itemsOnPage) {
                $_itemsCurrentPage = $this->_itemsOnPage;
            }
            $collection->getSelect()->limit($_itemsCurrentPage, $offset);
        } else {
            $collection->getSelect()->limit($this->_itemsOnPage, $offset);
        }
        $collection->getSelect()->where('status = 1');
        return $collection;
    }

    public function getPhotogallery()
    {
        $this->_currentPage = $this->getRequest()->getParam('page');
        if (!$this->_currentPage) {
            $this->_currentPage=1;
        }
        $collection = $this->getAllPhotoGalleryImages();
        
        if ($this->_itemsLimit!=null && $this->_itemsLimit<$collection->getSize()) {
            $this->_pagesCount = ceil($this->_itemsLimit/$this->_itemsOnPage);
        } else {
            $this->_pagesCount = ceil($collection->getSize()/$this->_itemsOnPage);
        }
        for ($i=1; $i<=$this->_pagesCount; $i++) {
            $this->_pages[] = $i;
        }
        $this->setLastPageNum($this->_pagesCount);
        $offset = $this->_itemsOnPage*($this->_currentPage-1);
        if ($this->_itemsLimit!=null) {
            $_itemsCurrentPage = $this->_itemsLimit - $offset;
            if ($_itemsCurrentPage > $this->_itemsOnPage) {
                $_itemsCurrentPage = $this->_itemsOnPage;
            }
            $collection->getSelect()->limit($_itemsCurrentPage, $offset);
        } else {
            $collection->getSelect()->limit($this->_itemsOnPage, $offset);
        }
        $collection->getSelect()->where('status = 1');
        return $collection;
    }
    public function isFirstPage()
    {
        if ($this->_currentPage==1) {
            return true;
        }
        return false;
    }
    public function isLastPage()
    {
        if ($this->_currentPage==$this->_pagesCount) {
            return true;
        }
        return false;
    }
    public function isPageCurrent($page)
    {
        if ($page==$this->_currentPage) {
            return true;
        }
        return false;
    }
    public function getPageUrl($page)
    {
        return $this->_storeManager->getStore()->getUrl('*', ['page' => $page]);
    }
    public function getNextPageUrl()
    {
        $page = $this->_currentPage+1;
        return $this->getPageUrl($page);
    }
    public function getPreviousPageUrl()
    {
        $page = $this->_currentPage-1;
        return $this->getPageUrl($page);
    }
    public function getPages()
    {
        $collection = $this->getAllPhotoGalleryImages();
        $pages = [];
        if ($this->_pagesCount <= $this->_displayPages) {
            $pages = range(1, $this->_pagesCount);
        } else {
            $half = ceil($this->_displayPages / 2);
            if ($this->_currentPage >= $half && $this->_currentPage <= $this->_pagesCount - $half) {
                $start  = ($this->_currentPage - $half) + 1;
                $finish = ($start + $this->_displayPages) - 1;
            } elseif ($this->_currentPage < $half) {
                $start  = 1;
                $finish = $this->_displayPages;
            } elseif ($this->_currentPage > ($this->_pagesCount - $half)) {
                    $finish = $this->_pagesCount;
                    $start  = $finish - $this->_displayPages + 1;
            }
            $pages = range($start, $finish);
        }
        return $pages;
    }
    public function counterPictures($photogalleryId)
    {
        $photogalleryImages = $this->_photogalleryimgFactory->create()->getCollection()
            ->addFieldToFilter('photogallery_id', $photogalleryId);
        return count($photogalleryImages);
    }

    public function getGalleryHeadings()
    {
        $galleries = $this->_photogalleryphotogalleryFactory->create()->getCollection()
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->addOrder('main_table.gorder', 'ASC')->addFieldToFilter('status', '1')
            ->getData();
        $galleries = array_map("unserialize", array_unique(array_map("serialize", $galleries)));
        return $galleries;
    }
    
    public function counterTagPictures($tag)
    {
        $photogalleryImages = $this->_photogalleryimgFactory->create()->getCollection()->addFieldToFilter('tags', $tag);
        return count($photogalleryImages);
    }
    public function addCaption($caption)
    {
        $html='';
        $html.='<div class="caption-block">';
        $html.='<div class="text-wrapper">';
        if ($this->_helper->enableIconEnable()) {
            $iconClass=$this->_helper->enableIconClass();
            $html.='<h4 class="title"><i class="'.$iconClass.'" "></i></h4>';
            $html.='<h5 class="subtitle">'.$caption.'</h5>';
        } else {
            $html.='<h4 class="title"></h4>';
            $html.='<h5 class="subtitle">'.$caption.'</h5>';
        }
        $html.='</div>';
        $html.='</div>';
        return $html;
    }
    public function getthumbsdata($thumb, $image, $width, $height)
    {
        $html='';
        if ($this->_helper->enableThumbsonFrontend()) {
            $html.='   <img class="item" width="'.$width.'" height="'.$height.'" src="'.$image.'" data-src="'.$thumb.'"/>';
        } else {
            $html.='   <img class="item"  src="'.$image.'" data-src="'.$image.'"/>';
        }
        return $html;
    }
    public function addCaptiononGallery()
    {
        $html='';
        if ($this->_helper->enableCaption()) {
            $html.=$this->_helper->getCaptionPosition().' '.$this->_helper->getCaptionAnimation().' '.$this->_helper->getCaptionAlingment().' '.$this->_helper->getCaptionColor();
        }
        return $html;
    }
    public function addFilter()
    {
        $html='';
        if ($this->_helper->enableFilter()) {
            $gallery_labels = $this->getGalleryHeadings();
            $html.='<div class="ftg-filters">';
            $html.='<input id="current_gallery" type="hidden" name="filter" value="0">';
            $html.='<a id="alllll" gal-id="all" href="#ftg-set-ftgall">All</a>';
            foreach ($gallery_labels as $gallery_label) {
                if ($gallery_label['show_in']=="1" ||$gallery_label['show_in']=="3") {
                    $html.='<a   gal-id="'.$gallery_label["photogallery_id"].'"    href="#ftg-set-'.$gallery_label["photogallery_id"].'">'.$gallery_label['gal_name'].'</a>';
                }
            }
            $html.='</div>';
        }
        return $html;
    }
    public function addfilterwithtiles($galleryid)
    {
        $html='';
        if ($this->_helper->enableFilter()) {
            $html.='ftg-set-'.$galleryid;
        }
        return $html;
    }
    public function addzoomEffect()
    {
        $html='';
        if ($this->_helper->enableZoom()) {
            $html.=$this->_helper->zoomEffect().' '.$this->_helper->zoomSpeed();
        }
        return $html;
    }
    public function addsmeffect()
    {
        $html='';
        if ($this->_helper->enableSocialMedia()) {
            $html.=$this->_helper->getSocialMediaPosition().' '.$this->_helper->getSocialMediaStyle();
        }
        return $html;
    }

    public function photogalleryHtml($gallery_images)
    {
        $html='';
        $html.='<div id="page">';
        $html.='<div id="gallery" class="final-tiles-gallery  '.$this->addzoomEffect().' '.$this->addCaptiononGallery().' '.$this->addsmeffect().'">';
        $html.=$this->addFilter();
        $html.=' <div class="ftg-items">';
        foreach ($gallery_images as $_gimage) {
            $html.=$this->_helper->createtiles($_gimage);
        }
        $html.=' </div>';
        $html.='</div>';
        $html.='</div>';
        return $html;
    }

    public function remainingelement($galleryid)
    {
        $collection=array();
        if ($galleryid=="") {
            $collection = $this->getAllPhotoGalleryImages();
        } else {
             $collection = $this->getPhotoGalleryImagesbyId($galleryid);
        }
        if ((int)count($collection) > (int)$this->_helper->getPagination()) {
            return true;
        }
        return false;
    }

    public function photogalleryHtmlduplicate($gallery_images, $gall_id)
    {
        $html='';
        $gallery='gallery_'.$gall_id;
        $html.='<div id="'.$gallery.'" class="final-tiles-gallery '.$this->addzoomEffect().' '.$this->addCaptiononGallery().' '.$this->addsmeffect().'">';
        $html.=' <div class="ftg-items">';
        foreach ($gallery_images as $_gimage) {
            $html.=$this->_helper->createtilesForTABandScroll($_gimage, $gallery);
        }
        $html.=' </div>';
        $html.='</div>';
        if ($this->_helper->enablepaginationonFrontend() && ($this->_helper->paginitionType()=="manual")) {
            if ($this->remainingelement($gall_id)) {
                $html.='<div class="cbp-l-loadMore-button">';
                $html.='<a class="cbp-l-loadMore-button-link">'.$this->_helper->getButtonText().'</a>';
                $html.='</div>';
            }
        }
        return $html;
    }
}
