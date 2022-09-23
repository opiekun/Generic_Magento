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

namespace Magezon\Core\Block\Product;

use Magento\Catalog\Helper\ImageFactory as HelperFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Image\NotLoadInfoImageException;

class ImageBuilder
{
    protected $_imageWidth;
    protected $_imageHeight;
    protected $_resizeImageWidth;
    protected $_resizeImageHeight;

    /**
     * @var ImageFactory
     */
    protected $imageFactory;

    /**
     * @var HelperFactory
     */
    protected $helperFactory;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var string
     */
    protected $imageId;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @param HelperFactory $helperFactory
     * @param ImageFactory $imageFactory
     */
    public function __construct(
        HelperFactory $helperFactory,
        \Magento\Catalog\Block\Product\ImageFactory $imageFactory
    ) {
        $this->helperFactory = $helperFactory;
        $this->imageFactory = $imageFactory;
    }

    /**
     * Set product
     *
     * @param Product $product
     * @return $this
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Set image ID
     *
     * @param string $imageId
     * @return $this
     */
    public function setImageId($imageId)
    {
        $this->imageId = $imageId;
        return $this;
    }

    /**
     * Set custom attributes
     *
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * Retrieve image custom attributes for HTML element
     *
     * @return string
     */
    protected function getCustomAttributes()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');
        if ($productMetadata->getVersion() < '2.4.0') {
            $result = [];
            foreach ($this->attributes as $name => $value) {
                $result[] = $name . '="' . $value . '"';
            }
            return !empty($result) ? implode(' ', $result) : '';
        }
        return $this->attributes;
    }

    /**
     * Calculate image ratio
     *
     * @param \Magento\Catalog\Helper\Image $helper
     * @return float|int
     */
    protected function getRatio(\Magento\Catalog\Helper\Image $helper)
    {
        $width = $helper->getWidth();
        $height = $helper->getHeight();
        if ($width && $height) {
            return $height / $width;
        }
        return 1;
    }

    public function setImageWidth($imageWidth)
    {
        $this->_imageWidth = $imageWidth;
        return $this;
    }

    public function getImageWidth()
    {
        return $this->_imageWidth;
    }

    public function setImageHeight($imageHeight)
    {
        $this->_imageHeight = $imageHeight;
        return $this;
    }

    public function getImageHeight()
    {
        return $this->_imageHeight;
    }

    public function setResizeImageWidth($resizeImageWidth)
    {
        $this->_resizeImageWidth = $resizeImageWidth;
        return $this;
    }

    public function getResizeImageWidth()
    {
        return $this->_resizeImageWidth;
    }

    public function setResizeImageHeight($resizeImageHeight)
    {
        $this->_resizeImageHeight = $resizeImageHeight;
        return $this;
    }

    public function getResizeImageHeight()
    {
        return $this->_resizeImageHeight;
    }

    /**
     * Create image block
     *
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function create(Product $product = null, $imageId = null, array $attributes = null)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $attrs = [];
        if ($this->getImageWidth()) {
            $attrs['width'] = $this->getImageWidth();
        }

        if ($this->getImageHeight()) {
            $attrs['height'] = $this->getImageHeight();
        }

        /** @var \Magento\Catalog\Helper\Image $helper */
        $helper = $this->helperFactory->create()->init($this->product, $this->imageId, $attrs);

        $template = $helper->getFrame()
            ? 'Magento_Catalog::product/image.phtml'
            : 'Magento_Catalog::product/image_with_borders.phtml';

        $imagesize   = $helper->getResizedImageInfo(); 
        $imageWidth  = $this->getImageWidth()?$this->getImageWidth():$helper->getWidth();
        $imageHeight = $this->getImageHeight()?$this->getImageHeight():$helper->getHeight();
        
        if ($this->getResizeImageWidth()) {
            $resizeImageWidth = $this->getResizeImageWidth();
        } else {
            $resizeImageWidth  = !empty($imagesize[0]) ? $imagesize[0] : $helper->getWidth();
        }
        if ($this->getResizeImageHeight()) {
            $resizeImageHeight = $this->getResizeImageHeight();
        } else {
            $resizeImageHeight = !empty($imagesize[1]) ? $imagesize[1] : $helper->getHeight();
        }

        $data = [
            'template'             => $template,
            'image_url'            => $helper->getUrl(),
            'width'                => $imageWidth,
            'height'               => $imageHeight,
            'label'                => $helper->getLabel(),
            'ratio'                => $this->getRatio($helper),
            'custom_attributes'    => $this->getCustomAttributes(),
            'resized_image_width'  => $resizeImageWidth,
            'resized_image_height' => $resizeImageHeight,
            'class'                => 'product-image-photo'
        ];

        $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');

        if ($productMetadata->getVersion() < '2.3.0') {
            return $this->imageFactory->create(['data' => $data]);
        } else {
            $helper = $this->imageFactory->create($this->product, $this->imageId, $attrs);
            $helper->setData($data);
            return $helper;
        }
    }
}
