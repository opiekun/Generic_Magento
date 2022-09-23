<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_Core
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Core\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Image extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magezon\Core\Block\Product\ImageBuilder
     */
    protected $imageBuilder;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $_imageFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context      $context      
     * @param \Magezon\Core\Block\Product\ImageBuilder   $imageBuilder 
     * @param \Magento\Framework\Filesystem              $filesystem   
     * @param \Magento\Framework\Image\AdapterFactory    $imageFactory 
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager 
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magezon\Core\Block\Product\ImageBuilder $imageBuilder,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->imageBuilder  = $imageBuilder;
        $this->_filesystem   = $filesystem;
        $this->_imageFactory = $imageFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function resizeImage($product, $imageWidth = '', $imageHeight = '', $attributes = [], $imageId ='category_page_grid')
    {
        return $this->imageBuilder->setProduct($product)
        ->setImageWidth($imageWidth)
        ->setImageHeight($imageHeight)
        ->setResizeImageWidth($imageWidth)
        ->setResizeImageHeight($imageHeight)
        ->setImageId($imageId)
        ->setAttributes($attributes)
        ->create();
    }

    /**
     * @param  string  $src           
     * @param  integer $width         
     * @param  integer $height        
     * @param  integer $quality       
     * @param  string  $dir           
     * @param  string  $newName       
     * @param  boolean $deleteIfExist 
     * @return string                 
     */
    public function resize($src, $width = 150, $height = 0, $quality = 80, $dir = 'magezon/resized', $newName = '', $deleteIfExist = false)
    {
        $dir          = $dir . '/' . $width;
        $absPath      = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($src);
        if ($newName) {
            $imageResized = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($dir . '/' . $newName);    
        } else {
            $imageResized = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($dir . '/' . $src);    
        }
        $imageResize  = $this->_imageFactory->create(); 
        $resizedURL   = '';
        if (file_exists($absPath)) {
            $imageResize->open($absPath);
            $imageResize->backgroundColor([255, 255, 255]);
            $imageResize->constrainOnly(TRUE);
            $imageResize->keepTransparency(TRUE);
            $imageResize->keepFrame(true);
            $imageResize->keepAspectRatio(true);
            $imageResize->quality($quality);
            if ($height) {
                $imageResize->resize($width, $height);    
            } else {
                $imageResize->resize($width);
            }
            if (!file_exists($imageResized) || $deleteIfExist) {
                $imageResize->save($imageResized);
            }
            if ($newName) {
                $resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA). $dir . '/' . $newName;
            } else {
                $resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA). $dir . '/' . $src;
            }
        }
        return $resizedURL;
    }
}
