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
namespace FME\Photogallery\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_GENERAL_ENABLE_MODULE           = 'photogallery/general/enable_module';
    const XML_PATH_GENERAL_PAGE_TITLE              = 'photogallery/photogallerymainpagesettings/page_title';
    const XML_PATH_GENERAL_SEO_URL_IDENTIFIER      = 'photogallery/photogallerymainpagesettings/seo_url_identifier';
    const XML_PATH_GENERAL_SEO_URL_SUFFIX          = 'photogallery/photogallerymainpagesettings/seo_url_suffix';
    const XML_PATH_GENERAL_SEO_META_KEYWORDS       = 'photogallery/photogallerymainpagesettings/meta_keywords';
    const XML_PATH_GENERAL_SEO_META_DESCRIPRION    = 'photogallery/photogallerymainpagesettings/meta_desp';
    const XML_PATH_GENERAL_GALLERY_TYPE             = 'photogallery/photogallerymainpagesettings/gallerytype';
    const XML_PATH_GENERAL_ENABLE_THUBS             = 'photogallery/photogallerysettings/enable_thumbs';
    const XML_PATH_GENERAL_ENABLE_FULL_WIDTH             = 'photogallery/photogallerysettings/full_w_gallery';
    const XML_PATH_GENERAL_GRID_SIZE             = 'photogallery/photogallerysettings/grid_size';
    const XML_PATH_GENERAL_GRID_SIZE_BELOW             = 'photogallery/photogallerysettings/disable_grid_size_below';
    const XML_PATH_PHOTOGALLERY_ENABLE_PRG         = 'photogallery/productsettings/enabled';
    const XML_PATH_PHOTOGALLERY_PAGINATION_ENABLE         = 'photogallery/photogallerysettings/enablepaginition';
    const XML_PATH_PHOTOGALLERY_PAGINATION         = 'photogallery/photogallerysettings/images_per_page';
    const XML_PATH_PHOTOGALLERY_THUMB_WIDHT        = 'photogallery/imgsettings/thumb_width';
    const XML_PATH_PHOTOGALLERY_THUMB_HEIGHT       = 'photogallery/imgsettings/thumb_height';
    const XML_PATH_PHOTOGALLERY_BG_COLOR           = 'photogallery/imgsettings/bg_color';
    const XML_PATH_PHOTOGALLERY_BUTTON_TEXT        = 'photogallery/photogallerysettings/page_button_text';
    const XML_PATH_PHOTOGALLERY_FRAME_THUMB        = 'photogallery/imgsettings/frame_thumb';
    const XML_PATH_PHOTOGALLERY_ASPECT_RATIO       = 'photogallery/imgsettings/aspect_ration';
    const XML_PATH_PHOTOGALLERY_ENABLE_CAT = 'photogallery/catsettings/enabled';
    const XML_PATH_PHOTOGALLERY_CAT_POSITION = 'photogallery/catsettings/position';
    const XML_PATH_CAPTION_ENABLE          = 'photogallery/photogallerytilesettings/caption';
    const XML_PATH_CAPTION_POSITION         = 'photogallery/photogallerytilesettings/caption_position';
    const XML_PATH_CAPTION_ALINGNMENT          = 'photogallery/photogallerytilesettings/caption_align';
    const XML_PATH_CAPTION_ANIMATION          = 'photogallery/photogallerytilesettings/caption_animation';
    const XML_PATH_CAPTION_COLOR         = 'photogallery/photogallerytilesettings/caption_colorscheme';
    const XML_PATH_CAPTION_ICON_ENABLE        = 'photogallery/photogallerytilesettings/icons';
    const XML_PATH_CAPTION_ICON_NAME        = 'photogallery/photogallerytilesettings/icons_list';
    const XML_PATH_SM_ENABLE       = 'photogallery/photogallerytilesettings/social_media';
    const XML_PATH_SM_POSITION       = 'photogallery/photogallerytilesettings/social_media_icon_pos';
    const XML_PATH_SM_STYLE     = 'photogallery/photogallerytilesettings/social_media_icon_style';
    const XML_PATH_ENABLE_ENLARGEMENT     = 'photogallery/photogallerytilesettings/allow_enlargment';
    const XML_PATH_MIN_TILE_WIDTH    = 'photogallery/photogallerytilesettings/mintilewidth';
    const XML_PATH_FILTER_ENABLE        = 'photogallery/photogallerysettings/filter';
    const XML_PATH_PAGINITION__ENABLE        = 'photogallery/photogallerysettings/enable_paginationwithoutFilter';
    const XML_PATH_ZOOM_ENABLE        = 'photogallery/photogallerytilesettings/zoom';
    const XML_PATH_ZOOM_EFFECT        = 'photogallery/photogallerytilesettings/zoom_effect';
    const XML_PATH_ZOOM_SPEED       = 'photogallery/photogallerytilesettings/zoom_speed';
    const XML_PATH_MARGIN_ENABLE      = 'photogallery/photogallerytilesettings/margin';
    const XML_PATH_MARGIN_SIZE      = 'photogallery/photogallerytilesettings/margin_list';
    const XML_PATH_ENABLE_IN_COLUMN      = 'photogallery/photogallerysettings/enable_Column';
    const XML_PATH_PAGINITION_OPTION      = 'photogallery/photogallerysettings/ajexloader';
    const XML_PATH_CROUSEL_NAV_BTN     = 'photogallery/crouselsettings/enabled_c_button';
    const XML_PATH_CROUSEL_ROTATION    = 'photogallery/crouselsettings/rotation';
    const XML_PATH_CROUSEL_TIME     = 'photogallery/crouselsettings/time_playing';
    const XML_PATH_CROUSEL_ITEMS    = 'photogallery/crouselsettings/citems';
    const XML_PATH_MAG_OPTION      = 'photogallery/photogallerypopusetting/popuooptions';
    const XML_PATH_POPUP_GAL_ENABLE      = 'photogallery/photogallerypopusetting/enablepopupgallery';
    const XML_PATH_POPUP_TIME     = 'photogallery/photogallerypopusetting/popuptime';
    const XML_PATH_POPUP_NAV_CLICK     = 'photogallery/photogallerypopusetting/enablepopupgalleryclick';
    const XML_PATH_ENABLE_TABS     = 'photogallery/photogallerysettings/enable_tabs';
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }
    
    public function carouselItems()
    {
        $size=$this->scopeConfig->getValue(
            self::XML_PATH_CROUSEL_ITEMS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($size==0 || $size==null) {
            return "5";
        } else {
            return $size;
        }
    }

    public function carouselRotationTime()
    {
        if ($this->carouselRotation()) {
            $size=$this->scopeConfig->getValue(
                self::XML_PATH_CROUSEL_TIME,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            if ($size==0 || $size==null) {
                return "1000";
            } else {
                return $size;
            }
        }
    }

    public function getGalleryType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_GALLERY_TYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function carouselRotation()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CROUSEL_ROTATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function carouselNavButton()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CROUSEL_NAV_BTN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function enablegalonPopUp()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_POPUP_GAL_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function paginitionType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PAGINITION_OPTION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function minTileWidth()
    {
        $width=$this->scopeConfig->getValue(
            self::XML_PATH_MIN_TILE_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($width==0 || $width==null) {
            return "200";
        } else {
            return $width;
        }
    }

    public function enablePopupNavOnCLick()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_POPUP_NAV_CLICK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getPopupTime()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_POPUP_TIME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getGridSizeBelow()
    {
        $size=$this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_GRID_SIZE_BELOW,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($size==0 || $size==null) {
            return "200";
        } else {
            return $size;
        }
    }

    public function enableEnlargeMent()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_ENLARGEMENT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function enableModule()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_ENABLE_MODULE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getGridSize()
    {
        $size=$this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_GRID_SIZE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($size==0 || $size==null) {
            return "200";
        } else {
            return $size;
        }
    }

    public function enableFullWidthGallery()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_ENABLE_FULL_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function enableSocialMedia()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SM_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getSocialMediaPosition()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SM_POSITION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getSocialMediaStyle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SM_STYLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function enableTabs()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_TABS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getMagniferOption()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MAG_OPTION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function enableIconEnable()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CAPTION_ICON_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function enableIconClass()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CAPTION_ICON_NAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function enableMargin()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MARGIN_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getMarginSize()
    {
        $size=$this->scopeConfig->getValue(
            self::XML_PATH_MARGIN_SIZE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($size==null|| $size==0) {
            return "10";
        } else {
            return $size;
        }
    }

    public function enableThumbsInColuumn()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_IN_COLUMN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function enableCaption()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CAPTION_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCaptionPosition()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CAPTION_POSITION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCaptionAlingment()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CAPTION_ALINGNMENT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCaptionColor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CAPTION_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCaptionAnimation()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CAPTION_ANIMATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function enableFilter()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FILTER_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function enablePaginitionWithoutFilter()
    {
        if (!$this->enableFilter()) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_PAGINITION__ENABLE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } else {
            return false;
        }
    }

    public function enableZoom()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ZOOM_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function zoomEffect()
    {
      
        return $this->scopeConfig->getValue(
            self::XML_PATH_ZOOM_EFFECT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function zoomSpeed()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ZOOM_SPEED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function enableThumbsonFrontend()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_ENABLE_THUBS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function enablepaginationonFrontend()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHOTOGALLERY_PAGINATION_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
   
    public function getPageTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_PAGE_TITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getSeoIdentifier()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_SEO_URL_IDENTIFIER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getSeoSuffix()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_SEO_URL_SUFFIX,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getMetaKeywords()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_SEO_META_KEYWORDS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getMetaDescription()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_SEO_META_DESCRIPRION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function enableProductRelatedGallery()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHOTOGALLERY_ENABLE_PRG,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function enableCatGallery()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHOTOGALLERY_ENABLE_CAT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getPagination()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHOTOGALLERY_PAGINATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
 
    public function getThumbWidth()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHOTOGALLERY_THUMB_WIDHT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getThumbHeight()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHOTOGALLERY_THUMB_HEIGHT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getBgcolor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHOTOGALLERY_BG_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getKeepframe()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHOTOGALLERY_FRAME_THUMB,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getAspectratioflag()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHOTOGALLERY_ASPECT_RATIO,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getButtonText()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHOTOGALLERY_BUTTON_TEXT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getPhotogalleryUrl()
    {
        $url = $this->getSeoIdentifier().$this->getSeoSuffix();
        return $this->_storeManager->getStore()->getUrl() . $url;
    }

    public function getPhotogalleryPath()
    {
        $url = $this->getSeoIdentifier().$this->getSeoSuffix();
        return $url;
    }

    public function getCatGalleryPosition()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHOTOGALLERY_CAT_POSITION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function addScoialMediaIcon($image_path)
    {
        $html="";
        $html.=' <div class="ftg-social">';
        $html.=' <a href="'.$image_path.'" data-social="twitter"><i class="fa fa-twitter"></i></a>';
        $html.=' <a href="'.$image_path.'" data-social="facebook"><i class="fa fa-facebook"></i></a>';
        $html.=' <a href="'.$image_path.'" data-social="google-plus"><i class="fa fa-google"></i></a>';
        $html.='  <a href="'.$image_path.'" data-social="pinterest"><i class="fa fa-pinterest"></i></a>';
        $html.=' </div>';
        return $html;
    }
    
    public function addScoialMediaIcon2($image_path)
    {
        $html="";
        $html.=' <div class="ftg-social">';
        $html.=' <a href="" data-social="twitter"><i class="fa fa-twitter"></i></a>';
        $html.=' <a href="" data-social="facebook"><i class="fa fa-facebook"></i></a>';
        $html.=' <a href="" data-social="google-plus"><i class="fa fa-google"></i></a>';
        $html.='  <a href="" data-social="pinterest"><i class="fa fa-pinterest"></i></a>';
        $html.=' </div>';
        return $html;
    }

    public function createtiles($_gimage)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $block = $objectManager->create('FME\Photogallery\Block\PList');
        $html='';
        $targetPath = $this->getMediaUrl($_gimage["img_name"]);
        $thumbPath = $this->getThumbsDirPath($targetPath);
        $arrayName = explode('/', $_gimage["img_name"]);
        $gallery_name = $_gimage['gal_name'];
        $thumbnail_path =  $thumbPath . '/' . $arrayName[3];
        $image_path = $this->getMediaUrl($_gimage["img_name"]);
        $description = $_gimage["img_description"];
        $html.='  <div class="tile '.$block->addfilterwithtiles($_gimage["photogallery_id"]).'  " >';
        if ($this->getMagniferOption()=="lighbox") {
            $html.='  <a class="tile-inner" href="'.$image_path.'"';
            $html.='data-title="'.$_gimage['img_label'].'"   data-lightbox="gallery">';
        } else {
            $html.='  <a class="tile-inner" href="'.$image_path.'" >';
        }
        $html.=$block->getthumbsdata($thumbnail_path, $image_path, 200, 200);
        if ($this->enableCaption()) {
            $html.=$block->addCaption($_gimage['img_label']);
        }
        $html.='  </a>';
        $html.=$this->addScoialMediaIcon($image_path);
        $html.='  </div>';
        return $html;
    }

    public function createtilesForTABandScroll($_gimage, $gallery)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $block = $objectManager->create('FME\Photogallery\Block\PList');
        $html='';
        $targetPath = $this->getMediaUrl($_gimage["img_name"]);
        $thumbPath = $this->getThumbsDirPath($targetPath);
        $arrayName = explode('/', $_gimage["img_name"]);
        $gallery_name = $_gimage['gal_name'];
        $thumbnail_path =  $thumbPath . '/' . $arrayName[3];
        $image_path = $this->getMediaUrl($_gimage["img_name"]);
        $description = $_gimage["img_description"];
        $html.='  <div class="tile   " >';
        if ($this->getMagniferOption()=="lighbox") {
            $html.='  <a class="tile-inner" href="'.$image_path.'"';
            $html.='data-title="'.$_gimage['img_label'].'"   data-lightbox="'.$gallery.'">';
        } else {
            $html.='  <a class="tile-inner" href="'.$image_path.'" >';
        }
        $html.=$block->getthumbsdata($thumbnail_path, $image_path, 200, 200);
        $html.=$block->addCaption($_gimage['img_label']);
        $html.='  </a>';
        $html.=$this->addScoialMediaIcon($image_path);
        $html.='  </div>';
        return $html;
    }

    public function getThumbsDirPath($filePath = false)
    {
        $mediaRootDir = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ).'photogallery/images/';
        $thumbnailDir = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ).'photogallery/images/';
        if ($filePath && strpos($filePath, $mediaRootDir) === 0) {
            $thumbnailDir .= dirname(substr($filePath, strlen($mediaRootDir)));
        }
        $thumbnailDir .=  '/'."thumb/";
        return $thumbnailDir;
    }

    public function getMediaUrl($url)
    {
        return $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ).'photogallery/images/'.$url;
    }

    public function getJsUrl($url)
    {
        return $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ).'photogallery/'.$url;
    }
}
