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

use Magento\Store\Model\ScopeInterface;

class Dataup extends \Magento\Framework\App\Helper\AbstractHelper
{   
    const XML_PATH_NANO_GALLAYOUT_LAYOUT           = 'photogallery/nanogalllerySetting/gallerylayout/layouts';
    const XML_PATH_NANO_GALLAYOUT_GRID_WIDTH           = 'photogallery/nanogalllerySetting/gallerylayout/width';
    const XML_PATH_NANO_GALLAYOUT_GRID_HEIGHT            = 'photogallery/nanogalllerySetting/gallerylayout/height';
    const XML_PATH_NANO_GALLAYOUT_JUST_HEIGHT          = 'photogallery/nanogalllerySetting/gallerylayout/justheight';
    const XML_PATH_NANO_GALLAYOUT_CASCADING_WIDTH          = 'photogallery/nanogalllerySetting/gallerylayout/cascadingwidth';
    const XML_PATH_NANO_GALLAYOUT_MOSAIC_WIDTH        = 'photogallery/nanogalllerySetting/gallerylayout/mosaicwidth';
    const XML_PATH_NANO_GALLAYOUT_MOSAIC_HEIGHT          = 'photogallery/nanogalllerySetting/gallerylayout/mosaicheight';
    const XML_PATH_NANO_GALLAYOUT_MOSAIC_GALLERY         = 'photogallery/nanogalllerySetting/gallerylayout/mosaictextarea';
    const XML_PATH_NANO_GALLAYOUT_THUMB_TBH         = 'photogallery/nanogalllerySetting/gallerylayout/thumbnailBorderHorizontal';
    const XML_PATH_NANO_GALLAYOUT_THUMB_TBV        = 'photogallery/nanogalllerySetting/gallerylayout/thumbnailBorderVertical';
    const XML_PATH_NANO_GALLAYOUT_THUMB_TGW        = 'photogallery/nanogalllerySetting/gallerylayout/thumbnailGutterWidth';
    const XML_PATH_NANO_GALLAYOUT_THUMB_TGH        = 'photogallery/nanogalllerySetting/gallerylayout/thumbnailGutterHeight';
    const XML_PATH_NANO_GALLAYOUT_THUMB_ALIGN        = 'photogallery/nanogalllerySetting/gallerylayout/thumnsallign';
    const XML_PATH_NANO_GALLAYOUT_THUMB_DIS_INT        = 'photogallery/nanogalllerySetting/gallerylayout/thumbnailDisplayInterval';
    const XML_PATH_NANO_GALLAYOUT_THUMB_DIS_TRN       = 'photogallery/nanogalllerySetting/gallerylayout/thumbnailDisplayTransition';
    const XML_PATH_NANO_GALLAYOUT_THUMB_DIS_TRN_DUR        = 'photogallery/nanogalllerySetting/gallerylayout/thumbnailDisplayTransitionDuration';
    const XML_PATH_NANO_GALLAYOUT_THUMB_DIS_COLOR       = 'photogallery/nanogalllerySetting/gallerylayout/bg_color';
    const XML_PATH_NANO_GALLAYOUT_LABEL_POS       = 'photogallery/nanogalllerySetting/labelSetting/position';
    const XML_PATH_NANO_GALLAYOUT_LABEL_DISPLAY       = 'photogallery/nanogalllerySetting/labelSetting/display';
    const XML_PATH_NANO_GALLAYOUT_LABEL_ALIGN      = 'photogallery/nanogalllerySetting/labelSetting/align';
    const XML_PATH_NANO_GALLAYOUT_TOOL_TL      = 'photogallery/nanogalllerySetting/thumbnailtools/topLeft';
    const XML_PATH_NANO_GALLAYOUT_TOOL_TR      = 'photogallery/nanogalllerySetting/thumbnailtools/topRight';
    const XML_PATH_NANO_GALLAYOUT_TOOL_BL      = 'photogallery/nanogalllerySetting/thumbnailtools/bottomLeft';
    const XML_PATH_NANO_GALLAYOUT_TOOL_BR     = 'photogallery/nanogalllerySetting/thumbnailtools/bottomRight';
    const XML_PATH_NANO_GALLAYOUT_HE_THE     = 'photogallery/nanogalllerySetting/hovereffect/thumbnailHoverEffect2';
    const XML_PATH_NANO_GALLAYOUT_LIGHTBOX_TL     = 'photogallery/nanogalllerySetting/lightBox/topLeft';
    const XML_PATH_NANO_GALLAYOUT_LIGHTBOX_TR     = 'photogallery/nanogalllerySetting/lightBox/topRight';
    const XML_PATH_NANO_GALLAYOUT_LIGHTBOX_VTS     = 'photogallery/nanogalllerySetting/lightBox/viewerToolbarstandard';
    const XML_PATH_NANO_GALLAYOUT_LIGHTBOX_VTB     = 'photogallery/nanogalllerySetting/lightBox/viewerToolbarminimize';
    const XML_PATH_NANO_GALLAYOUT_GEN_GALTYPE     = 'photogallery/nanogalllerySetting/enable_module';
    const XML_PATH_NANO_GALLAYOUT_THUMB_LASTFILL      = 'photogallery/nanogalllerySetting/paginitionsettings/galleryLastRowFull';
    const XML_PATH_NANO_GALLAYOUT_PAGINITION      = 'photogallery/nanogalllerySetting/paginitionsettings/paginitionType';
    const XML_PATH_NANO_GALLAYOUT_MULTI_ROW_ALLOW     = 'photogallery/nanogalllerySetting/paginitionsettings/allowgalleryMaxRows';
    const XML_PATH_NANO_GALLAYOUT_MULTI_ROW    = 'photogallery/nanogalllerySetting/paginitionsettings/galleryMaxRows';
    const XML_PATH_NANO_GALLAYOUT_MULTI_ROW_DOT    = 'photogallery/nanogalllerySetting/paginitionsettings/dotgalleryMaxRows';
    const XML_PATH_NANO_GALLAYOUT_MULTI_ROW_NUM    = 'photogallery/nanogalllerySetting/paginitionsettings/numgalleryMaxRows';
    const XML_PATH_NANO_GALLAYOUT_MULTI_ROW_RECT    = 'photogallery/nanogalllerySetting/paginitionsettings/rectgalleryMaxRows';
    const XML_PATH_NANO_GALLAYOUT_PAGE_MORESTEP    = 'photogallery/nanogalllerySetting/paginitionsettings/galleryDisplayMoreStep';
    const XML_PATH_NANO_GALLAYOUT_INBULTLAYOUT    = 'photogallery/nanogalllerySetting/inbulitlayouts';
    const XML_PATH_MINSORY_B_SIZE  = 'photogallery/misarygallery/btnsize';
    const XML_PATH_MINSORY_B_STYLE  = 'photogallery/misarygallery/btnstyle';
    const XML_PATH_MINSORY_A_POS  = 'photogallery/misarygallery/arrowposition';
    const XML_PATH_MINSORY_A_STYLE  = 'photogallery/misarygallery/arrowstyle';
    const XML_PATH_MINSORY_A_ICON  = 'photogallery/misarygallery/arrowicons';
    const XML_PATH_MINSORY_A_HOVEREFFECTS  = 'photogallery/misarygallery/arrowhovereffect';
    const XML_PATH_MINSORY_IMAGE_RADIUS = 'photogallery/misarygallery/imageradius';
    const XML_PATH_MINSORY_IMAGE_B_OPACITY = 'photogallery/misarygallery/imagebackgroundopacity';
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }
    
    public function getMensoryImageBackgroundOpacity()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MINSORY_IMAGE_B_OPACITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getMensoryImageRadius()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MINSORY_IMAGE_RADIUS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getMensoryAHoverEffects()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MINSORY_A_HOVEREFFECTS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getMensoryAIcons()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MINSORY_A_ICON,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getMensoryAStyle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MINSORY_A_STYLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getMensoryAPos()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MINSORY_A_POS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getMensoryBStyle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MINSORY_B_STYLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getMensoryBSize()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MINSORY_B_SIZE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getGalleryDefaultLayout()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_INBULTLAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getGalleryPaginitionRowsRect()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_MULTI_ROW_RECT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getGalleryPaginitionRowsNums()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_MULTI_ROW_NUM,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getGalleryPaginitionRowsDots()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_MULTI_ROW_DOT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getGalleryPaginitionMoreStep()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_PAGE_MORESTEP,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getGalleryPaginition()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_PAGINITION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getMaxRows()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_MULTI_ROW,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function isAllowMaxRows()
    {
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_PATH_NANO_GALLAYOUT_MULTI_ROW_ALLOW, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
    }

    public function getGalleryThumbLastFill()
    {
        $isEnabled = 'true';
         $enabled = $this->scopeConfig->getValue(self::XML_PATH_NANO_GALLAYOUT_THUMB_LASTFILL, ScopeInterface::SCOPE_STORE);
         if ($enabled == null || $enabled == '0') {
             $isEnabled = 'false';
         }
         return $isEnabled;

    }

    public function getGalleryBackgroundColor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_THUMB_DIS_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getGalleryType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_GEN_GALTYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoLightBOXVTB()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_LIGHTBOX_VTB,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoLightBOXVTS()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_LIGHTBOX_VTS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoLightBOXTR()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_LIGHTBOX_TR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoLightBOXTL()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_LIGHTBOX_TL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function isLabelDisplay()
    {
        $isEnabled = 'true';
        $enabled = $this->scopeConfig->getValue(self::XML_PATH_NANO_GALLAYOUT_LABEL_DISPLAY, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = 'false';
        }
        return $isEnabled;
    }

    public function getNanoThumbHoverEffect()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_HE_THE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbToolBR()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_TOOL_BR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbToolBL()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_TOOL_BL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbToolTR()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_TOOL_TR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbToolTL()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_TOOL_TL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbLabelAlign()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_LABEL_ALIGN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbGalleryLabelPosition()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_LABEL_POS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbDisplayTransitionDuration()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_THUMB_DIS_TRN_DUR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbDisplayTransition()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_THUMB_DIS_TRN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbDisplayInterval()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_THUMB_DIS_INT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbAlign()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_THUMB_ALIGN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbTGH()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_THUMB_TGH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbTGW()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_THUMB_TGW,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbTBV()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_THUMB_TBV,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbTBH()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_THUMB_TBH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanolayout()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_LAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoGridWidth()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_GRID_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoGridHeight()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_GRID_HEIGHT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoJustHeight()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_JUST_HEIGHT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoCascadingWidth()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_CASCADING_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoMosacSettings()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_MOSAIC_GALLERY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoMosacWidth()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_GRID_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoMosacHeight()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_GRID_HEIGHT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getOverallWidth()
    {
        if($this->getNanolayout()=='grid')
        {
            return $this->getNanoGridWidth();
        }
        elseif($this->getNanolayout()=='justified')
        {
            return "'"."auto"."'";;
        }
        elseif($this->getNanolayout()=='cascading')
        {
            return $this->getNanoCascadingWidth();
        }
        elseif($this->getNanolayout()=='mosaic')
        {
            return $this->getNanoMosacWidth();
        }
        return 200;
    }

    public function getOverallHeight()
    {
        if($this->getNanolayout()=='grid')
        {
            return $this->getNanoGridHeight();
        }
        elseif($this->getNanolayout()=='justified')
        {
            return $this->getNanoJustHeight();
        }
        elseif($this->getNanolayout()=='cascading')
        {
            return "'"."auto"."'";
        }
        elseif($this->getNanolayout()=='mosaic')
        {
            return $this->getNanoMosacHeight();
        }
        return 200;
    }
}
