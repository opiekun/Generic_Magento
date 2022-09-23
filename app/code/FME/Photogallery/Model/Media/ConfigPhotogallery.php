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
namespace FME\Photogallery\Model\Media;

class ConfigPhotogallery implements PhotogalleryConfigInterface
{
   
    protected $storeManager;

    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    public function getPhotogalleryBaseMediaPathAddition()
    {
        return 'photogallery/images';
    }

    public function getPhotogalleryBaseMediaUrlAddition()
    {
        return 'photogallery/images';
    }

    public function getPhotogalleryBaseMediaPath()
    {
        return 'photogallery/images';
    }

    public function getPhotogalleryBaseMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'photogallery/images';
    }

    public function getPhotogalleryBaseTmpMediaPath()
    {
        return   $this->getPhotogalleryBaseMediaPathAddition();
    }

    public function getPhotogalleryBaseTmpMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        )  . $this->getPhotogalleryBaseMediaUrlAddition();
    }

    public function getPhotogalleryMediaUrl($file)
    {
        return $this->getPhotogalleryBaseMediaUrl() . '/' . $this->_prepareFile($file);
    }

    public function getPhotogalleryMediaPath($file)
    {
        return $this->getPhotogalleryBaseMediaPath() . '/' . $this->_prepareFile($file);
    }

    public function getPhotogalleryTmpMediaUrl($file)
    {
        return $this->getPhotogalleryBaseTmpMediaUrl() . '/' . $this->_prepareFile($file);
    }


    public function getPhotogalleryTmpMediaShortUrl($file)
    {
        return $this->getPhotogalleryBaseMediaUrlAddition() . '/' . $this->_prepareFile($file);
    }

    public function getPhotogalleryMediaShortUrl($file)
    {
        return $this->getPhotogalleryBaseMediaUrlAddition() . '/' . $this->_prepareFile($file);
    }

    public function getPhotogalleryTmpMediaPath($file)
    {
        return $this->getPhotogalleryBaseTmpMediaPath() . '/' . $this->_prepareFile($file);
    }

    protected function _prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }
}
