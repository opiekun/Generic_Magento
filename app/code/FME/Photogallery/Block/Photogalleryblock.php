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
 
class Photogalleryblock extends \Magento\Framework\View\Element\Template
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
    protected $blockname;
    protected $layout;
    protected $allow_filter;
    protected $allow_ajex;
    protected $load_type;
    protected $manual_btn;
    protected  $image_perpage;
    protected  $full_widthgallery;

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
        $this->blockname= $this->getBlockTitle();
        $this->enableicon= $this->getEnableIcon();
        $this->layout= $this->getLayoutType();
        $this->allow_filter= $this->getAllowFilter();;
        $this->allow_ajex= $this->getAllowAjex();;
        $this->load_type= $this->getLoadType();;
        $this->manual_btn= $this->getManualBtn();
        $this->image_perpage=$this->getImagePerpage();
        $this->full_widthgallery=$this->getFullWidthgallery();;
        $this->setTemplate("FME_Photogallery::block.phtml");
        return parent::_toHtml();
    }
    public function getFwGallery()
    {
        return $this->full_widthgallery;
    }
    public function getGalleryIds()
    {
        return $this->galleryidentifier;
    }
    public function getPagination()
    {
        return $this->image_perpage;   
    }
    public function linkForAjex()
    {
        return "photogallery/index/getimageswofilterblock?catids=".$this->getGalleryIds()."&peritem=".$this->getPagination();
    }
    public function linkForAjexTab()
    {
        return "photogallery/index/getimageswtabblock?catids=".$this->getGalleryIds()."&peritem=".$this->getPagination();
    }
    public function enablePaginitionForTabs()
    {
        if( $this->allow_ajex=="1" )
        {
            return 1;
        }
        else
        return 0;
    }
    public function enablePaginitionWithoutFilter()
    {
        if( $this->allow_filter!="1" && $this->allow_ajex=="1" )
        {
            return 1;
        }
        else
        return 0;
    }
    public function getManualText()
    {
        return $this->manual_btn;
    }
    public function getLoadTypeAjex()
    {
        return $this->load_type;
    }
    public function lengthOfBlockId($ids)
    {
        $myArray = explode(',', $ids);
        return sizeof($myArray);
    }
    public function addCaptiononGallery()
    {
        $html='';
        if ($this->enablecaption==1 ||$this->enablecaption=="1") {
            $html.=$this->captionposition.' '.$this->captionanimation.' '.$this->captionalignment.' '.$this->captioncolor;
        }
        return $html;
    }
    public function addZoomEffect()
    {
        $html='';
        if ($this->enablezoom==1 ||$this->enablezoom=="1") {
            $html.=$this->zoomeffect.' '.$this->zoomspeed;
        }
        return $html;
    }
    public function addSmEffect()
    {
        $html='';
        if ($this->enablesm==1 ||$this->enablesm=="1") {;
            
            $html.=$this->smposition.' '.$this->smstyle;
        }
        return $html;
    }
    public function getBlock()
    {
        return $this->blockid ;
    }
    public function getGalleryLayout()
    {
        return $this->layout ;
    }
    public function getGalleryHeadings($ids)
    {
        $galleries = $this->_photogalleryphotogalleryFactory->create()->getCollection()
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->addOrder('main_table.gorder', 'ASC')->addFieldToFilter('status', '1');
        $galleries->getSelect()->where('main_table.photogallery_id in ('.$ids.')');
        $galleries=$galleries->getData();
        $galleries = array_map("unserialize", array_unique(array_map("serialize", $galleries)));
        return $galleries;
    }
    public function addFilter($ids)
    {
        $html='';
        $gallery_labels = $this->getGalleryHeadings($ids);
        $html.='<div class="ftg-filters">';
        $html.='<input id="current_gallery" type="hidden" name="filter" value="0">';
        $html.='<a id="alllll" gal-id="all" href="#ftg-set-ftgall">All</a>';
        foreach ($gallery_labels as $gallery_label) {
            $html.='<a gal-id="'.$gallery_label["photogallery_id"].'"  href="#ftg-set-'.$gallery_label["photogallery_id"].'">'.$gallery_label['gal_name'].'</a>';
        }
        $html.='</div>';
        return $html;
    }
    public function getALlGalleryForTabWithFilter()
    {
        $collection=$this->getPhotoGalleryImagesbyIdForAjex($this->galleryidentifier);
        return $collection;
    }
    public function getALlGalleryForTabWithOutFilter()
    {
        $collection=$this->getPhotoGalleryImagesbyId($this->galleryidentifier);
        return $collection;
    }
    public function getGalleryHeadingForTabs()
    {
        $gallery_labels = $this->getGalleryHeadings($this->galleryidentifier);
        return  $gallery_labels;
    }
    public function createTiles($_gimage)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $block = $objectManager->create('FME\Photogallery\Block\PList');
        $html='';
        $targetPath = $this->_helper->getMediaUrl($_gimage["img_name"]);
        $thumbPath = $this->_helper->getThumbsDirPath($targetPath);
        $arrayName = explode('/', $_gimage["img_name"]);
        $gallery_name = $_gimage['gal_name'];
        $thumbnail_path =  $thumbPath . '/' . $arrayName[3];
        $image_path = $this->_helper->getMediaUrl($_gimage["img_name"]);
        $description = $_gimage["img_description"];
        $html.='  <div class="tile '.$this->addFilterWithTiles($_gimage["photogallery_id"]).'  " >';
        if ($this->_helper->getMagniferOption()=="lighbox") {
            $html.='  <a class="tile-inner" href="'.$image_path.'" data-title="';
            $html.=$_gimage['img_label'].'"   data-lightbox="gallery'.$this->getBlock().'">';
        } else {
            $html.='  <a class="tile-inner" href="'.$image_path.'" >';
        }
        $html.=$block->getthumbsdata($thumbnail_path, $image_path, 200, 200);
        if ($this->enablecaption==1 ||$this->enablecaption=="1") {
            $html.=$this->addCaption($_gimage['img_label']);
        }
        $html.='  </a>';
        $html.=$this->_helper->addScoialMediaIcon($image_path);
        $html.='  </div>';
        return $html;
    }
    public function createTilesForTabAndScroll($_gimage, $gallery)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $block = $objectManager->create('FME\Photogallery\Block\PList');
        $html='';
        $targetPath = $this->_helper->getMediaUrl($_gimage["img_name"]);
        $thumbPath = $this->_helper->getThumbsDirPath($targetPath);
        $arrayName = explode('/', $_gimage["img_name"]);
        $gallery_name = $_gimage['gal_name'];
        $thumbnail_path =  $thumbPath . '/' . $arrayName[3];
        $image_path = $this->_helper->getMediaUrl($_gimage["img_name"]);
        $description = $_gimage["img_description"];
        $html.='  <div class="tile   " >';
        if ($this->_helper->getMagniferOption()=="lighbox") {
            $html.='  <a class="tile-inner" href="'.$image_path.'"';
            $html.='data-title="'.$_gimage['img_label'].'"   data-lightbox="'.$gallery.'">';
        } else {
            $html.='  <a class="tile-inner" href="'.$image_path.'" >';
        }
        $html.=$block->getthumbsdata($thumbnail_path, $image_path,200, 200);
        if ($this->enablecaption==1 ||$this->enablecaption=="1") {
            $html.=$block->addCaption($_gimage['img_label']);
        }
        $html.='  </a>';
        $html.=$this->_helper->addScoialMediaIcon($image_path);
        $html.='  </div>';
        return $html;
    }
    public function photogalleryHtmlDuplicate($gallery_images, $gall_id)
    {
        $html='';
        $gallery='gallery_'.$gall_id;
        $html.='<div id="'.$gallery.'" class="final-tiles-gallery '.$this->addZoomEffect().' '.$this->addCaptiononGallery().' '.$this->addSmEffect().'">';
        $html.=' <div class="ftg-items">';
        foreach ($gallery_images as $_gimage) {
            $html.=$this->createTilesForTabAndScroll($_gimage, $gallery);
        }
        $html.=' </div>';
        $html.='</div>';
        if ($this->enablePaginitionWithoutFilter()=="1" || $this->enablePaginitionWithoutFilter()==1)
        {
            if (($this->getLoadTypeAjex()=="manual")) {
                if ($this->remainingElement($gall_id)) {
                    $html.='<div class="cbp-l-loadMore-button">';
                    $html.='<a class="cbp-l-loadMore-button-link">'.$this->getManualText().'</a>';
                    $html.='</div>';
                }
            }
        }
        return $html;
    }
    public function remainingElement($galleryid)
    {
        $collection=array();
        if ($galleryid=="") {
            $collection = $this->getAllPhotoGalleryImages();
        } else {
             $collection = $this->getPhotoGalleryTabbyOneId($galleryid);
        }
        if ((int)count($collection) > (int)$this->getPagination()) {
            return true;
        }
        return false;
    }
    public function generatePhotogallery()
    {
        if ($this->galleryidentifier!=null) {
            if( $this->allow_filter!="1" && $this->allow_ajex=="1" )
            {
                $collection=$this->getPhotoGalleryImagesbyIdForAjex($this->galleryidentifier);
            }
            else{
                $collection=$this->getPhotoGalleryImagesbyId($this->galleryidentifier);
            }
            if ($this->gallerytype=="simple") {
                $html='';
                $html.='<div id="page'.$this->getBlock().'">';
                $html.='<div id="gallery'.$this->getBlock().'" class="final-tiles-gallery  '.$this->addZoomEffect().' '.$this->addCaptiononGallery().' '.$this->addSmEffect().'">';
                if (count($collection)>0) {
                    if ($this->lengthOfBlockId($this->galleryidentifier)>1) {
                        if($this->allow_filter=="1")
                        {
                            $html.=$this->addFilter($this->galleryidentifier);
                        }
                    }
                    $html.=' <div class="ftg-items">';
                    foreach ($collection as $_gimage) {
                    
                        $html.=$this->createTiles($_gimage);
                    }
                    $html.=' </div>';
                }
                $html.='</div>';
                $html.='</div>';
                return $html;
            } elseif ($this->gallerytype=="carousel") {
                $html='';
                if (count($collection)>0) {
                    $html.=$this->setCrowsel($collection);
                }
                return $html;
            }
        }
    }
    public function setCrowsel($collection)
    {
        $html='';
        $html.='<div class="media_gallery_slider">';
        $html.='<h3>'.$this->blockname.'</h3>';
        $html.='<div class="container-carousel">';
        $html.='<div id="owl-demo'.$this->getBlock().'" class="owl-carousel owl-theme">';
        foreach ($collection as $_gallery) {
            $imageFile = $this->_helper->getMediaUrl($_gallery["img_name"]);
            $str = $_gallery["img_name"];
            $aryimg = explode("/", $str);
            $targetPath = $this->_helper->getMediaUrl($_gallery["img_name"]);
            $thumbPath = $this->_helper->getThumbsDirPath($targetPath);
            $arrayName = explode('/', $_gallery["img_name"]);
            $thumbnail_path = $thumbPath . $arrayName[3];
            $html.='<div class="item">';
            $html.='<a class="image'.$this->getBlock().'" href="'.$imageFile.'" title="" rel="shadowbox">';
            $html.='<img src="'.$thumbnail_path.'" width="100%" height="120px" alt="thumbnail" /></a>';
            $html.='</div>';
        }
        $html.='</div>';
        $html.='<div class="customNavigation clearfix"> <a class="prev">';
        $html.='<i class="icon-left-open"></i></a> <a class="next"><i class="icon-right-open"></i></a> </div>';
        $html.='</div>';
        $html.='</div>';
        return $html;
    }

    public function addFilterWithTiles($galleryid)
    {
        $html='';
        $html.='ftg-set-'.$galleryid;
        return $html;
    }
    
    public function addCaption($caption)
    {
        $html='';
        $html.='<div class="caption-block">';
        $html.='<div class="text-wrapper">';
        if ($this->enableicon==1 ||$this->enableicon=="1") {
            $html.='<h4 class="title"><i class="'.$this->iconclass.'" "></i></h4>';
            $html.='<h5 class="subtitle">'.$caption.'</h5>';
        } else {
            $html.='<h4 class="title"></h4>';
            $html.='<h5 class="subtitle">'.$caption.'</h5>';
        }
        $html.='</div>';
        $html.='</div>';
        return $html;
    }
    public function getBlockContent()
    {
        $collection=$this->getPhotoGalleryImagesbyId($this->galleryidentifier);
        return "asasdasdas";
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
        $collection->getSelect()->where('show_in in (1,3)')->order('main_table.img_order ASC');
        return $collection;
    }
    public function getPhotoGalleryImagesbyIdTab($id)
    {
        $this->setVariable();
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
        $collection->getSelect()->order('main_table.img_order ASC');
        $collection->getSelect()->where('main_table.photogallery_id = '.$id);
        return $collection;
    }
    public function getOtherGalleryForTab($id)
    {
        $collection;
        if($this->allow_ajex=="1" )
        {
            $collection= $this->getPhotoGalleryImagesbyIdForAjexByIds($id);
        }
        else{
            $collection= $this->getPhotoGalleryImagesbyIdTab($id);
        }
        return $collection;
    }
    public function getPhotoGalleryImagesbyIdForAjexByIds($id)
    {
        $this->setVariable();
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
        $offset=0;
        $itemsToLoad= $this->image_perpage;
         $collection->getSelect()->limit($itemsToLoad, $offset);
        $collection->getSelect()->where('status = 1');
        $collection->getSelect()->order('main_table.img_order ASC');
        $collection->getSelect()->where('main_table.photogallery_id = '.$id);
        return $collection;
    }
    public function getPhotoGalleryTabbyOneId($id)
    {
        $this->setVariable();
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
        $collection->getSelect()->order('main_table.img_order ASC');
        $collection->getSelect()->where('main_table.photogallery_id = '.$id);
        return $collection;
    }
    public function getPhotoGalleryImagesForTabAll($id)
    {
        $this->setVariable();
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
        $collection->getSelect()->order('main_table.img_order ASC');
        $collection->getSelect()->where('main_table.photogallery_id in ('.$id.')');
        return $collection;
    }
    public function getPhotoGalleryImagesbyIdForAjex($id)
    {
        $this->setVariable();
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
        )->where(' store_table.store_id in (?)', [0, $store])->distinct(true);
        $offset=0;
        $itemsToLoad= $this->image_perpage;
        $collection->getSelect()->limit($itemsToLoad, $offset);
        $collection->getSelect()->where('status = 1');
        $collection->getSelect()->order('main_table.img_order ASC');
        $collection->getSelect()->where('main_table.photogallery_id in ('.$id.')');
        return $collection;
    }
    public function getPhotoGalleryImagesbyId($id)
    {
        $this->setVariable();
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
        )->where(' store_table.store_id in (?)', [0, $store])->distinct(true);
        $collection->getSelect()->where('status = 1');
        $collection->getSelect()->order('main_table.img_order ASC');
        $collection->getSelect()->where('main_table.photogallery_id in ('.$id.')');
        return $collection;
    }
    public function getGalType()
    {
        return $this->gallerytype;
    }
    public function getAnyType()
    {
        return $this->gallerytype;
    }
    public function setVariable()
    {
        if ($this->gallerytype==null) {
            $this->gallerytype="simple";
        }
        if ($this->blockname==null) {
            $this->blockname="BLOCK";
        }
        if ($this->enablecaption==null) {
            $this->enablecaption=$this->_helper->enableCaption();
        }
        if ($this->captionposition==null) {
            $this->captionposition=$this->_helper->getCaptionPosition();
        }
        if ($this->captionanimation==null) {
            $this->captionanimation=$this->_helper->getCaptionAnimation();
        }
        if ($this->captionalignment==null) {
            $this->captionalignment=$this->_helper->getCaptionAlingment();
        }
        if ($this->captioncolor==null) {
            $this->captioncolor=$this->_helper->getCaptionColor();
        }
        if($this->layout==null)
        {
            if ($this->_helper->enableThumbsInColuumn()=="col")
            {
                $this->layout= 'columns';
            }  
	        else if($this->_helper->enableThumbsInColuumn()=="final")
            {   
                $this->layout='final';
            }
        }
        if ($this->enableicons==null) {
            $this->enableicons=$this->_helper->enableIconEnable();
        }
        if ($this->enablezoom==null) {
            $this->enablezoom=$this->_helper->enableZoom();
        }
        if ($this->zoomeffect==null) {
            $this->zoomeffect=$this->_helper->zoomEffect();
        }
        if ($this->zoomspeed==null) {
            $this->zoomspeed=$this->_helper->zoomSpeed();
        }
        if ($this->enablesm==null) {
            $this->enablesm=$this->_helper->enableSocialMedia();
        }
        if ($this->smposition==null) {
            $this->smposition=$this->_helper->getSocialMediaPosition();
        }
        if ($this->smstyle==null) {
            $this->smstyle=$this->_helper->getSocialMediaStyle();
        }
        if ($this->iconclass==null) {
            $this->iconclass=$this->_helper->enableIconClass();
        }
    }

    public function photogalleryHtml()
    {
        $html='';
        $html.='<div id="page">';
        $html.='<div id="gallery" class="final-tiles-gallery  ';
        $html.=$this->addZoomEffect().' '.$this->addCaptiononGallery().' '.$this->addSmEffect().'">';
        $html.=' <div class="ftg-items">';
        $html.=' </div>';
        $html.='</div>';
        $html.='</div>';
        return $html;
    }

    public function getMediaBlock()
    {
        $block = $this->_blockModel->load($this->galleryidentifier);
        return $block;
    }
}
