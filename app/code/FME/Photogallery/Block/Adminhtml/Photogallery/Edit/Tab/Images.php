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

namespace FME\Photogallery\Block\Adminhtml\Photogallery\Edit\Tab;

use Magento\Backend\Block\Media\Uploader;
use Magento\Framework\View\Element\AbstractBlock;

class Images extends \Magento\Backend\Block\Widget
{
    protected $_template = 'photogallery/gallery.phtml';
    protected $_mediaConfig;
    protected $_jsonEncoder;
    public $objectMgr;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \FME\Photogallery\Model\Media\ConfigPhotogallery $mediaConfig,
        \Magento\Framework\Registry $coreRegister,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_mediaConfig = $mediaConfig;
        $this->_coreRegister = $coreRegister;
        $this->objectMgr = $objectManager;
        parent::__construct($context, $data);
    }
    
    protected function _prepareLayout()
    {
        $productMetadata = $this->objectMgr->create('\Magento\Framework\App\ProductMetadata');
        $version = $productMetadata->getVersion();
        if (version_compare($version, '2.3.5', '>=')){
            $this->imageUploadConfigDataProvider = $this->objectMgr::getInstance()->get(\Magento\Backend\Block\DataProviders\ImageUploadConfig::class);
            $this->addChild(
                'uploader',
                \Magento\Backend\Block\Media\Uploader::class,
                ['image_upload_config_data' => $this->imageUploadConfigDataProvider]
            );
            $this->getUploader()->getConfig()->setUrl(
                $this->_urlBuilder->getUrl('photogalleryadmin/photogallery/upload')
            )->setFileField(
                'image'
            )->setFilters(
                [
                    'images' => [
                        'label' => __('Images (.gif, .jpg, .png)'),
                        'files' => ['*.gif', '*.jpg', '*.jpeg', '*.png'],
                    ],
                ]
            );
            $this->_eventManager->dispatch('photogallery_prepare_layout', ['block' => $this]);
            return parent::_prepareLayout();
        }
        if (version_compare($version, '2.3.1', '>=')){
            $this->imageUploadConfigDataProvider = $this->objectMgr::getInstance()->get(\Magento\Backend\Block\DataProviders\ImageUploadConfig::class);
            $this->addChild(
                'uploader',
                \Magento\Backend\Block\Media\Uploader::class,
                ['image_upload_config_data' => $this->imageUploadConfigDataProvider]
            );
        }elseif (version_compare($version, '2.2.8', '>=') && version_compare($version, '2.3.0', '<')){
            $this->imageUploadConfigDataProvider = $this->objectMgr::getInstance()->get(\Magento\Backend\Block\DataProviders\UploadConfig::class);
            $this->addChild(
                'uploader',
                \Magento\Backend\Block\Media\Uploader::class,
                ['image_upload_config_data' => $this->imageUploadConfigDataProvider]
            );
        }else{
            //this is for 2.3.0 and 2.2.7 or less
            $this->addChild('uploader', 'Magento\Backend\Block\Media\Uploader');        
        }
        $this->getUploader()->getConfig()->setUrl(
            $this->_urlBuilder->addSessionParam()->getUrl('photogalleryadmin/photogallery/upload')
        )->setFileField(
            'image'
        )->setFilters(
            [
                    'images' => [
                        'label' => __('Images (.gif, .jpg, .png)'),
                        'files' => ['*.gif', '*.jpg', '*.jpeg', '*.png'],
                    ],
                ]
        );
        $this->_eventManager->dispatch('photogallery_prepare_layout', ['block' => $this]);
        return parent::_prepareLayout();
    }

    public function images()
    {
        $images = $this->_coreRegister->registry('photogallery_img');
        $img_data = $images->getData();
        return $img_data;
    }

    public function getUploader()
    {
        return $this->getChildBlock('uploader');
    }

    public function getUploaderHtml()
    {
        return $this->getChildHtml('uploader');
    }

    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    public function getAddImagesButton()
    {
        return $this->getButtonHtml(
            __('Add New Images'),
            $this->getJsObjectName() . '.showUploader()',
            'add',
            $this->getHtmlId() . '_add_images_button'
        );
    }

    public function getMediaAttributes()
    {
        return $this->getElement()->getDataObject()->getMediaAttributes();
    }

    public function getImagesJson()
    {
        $value['images'] = $this->images();
        if (is_array($value['images']) && count($value['images']) > 0) {
            foreach ($value['images'] as &$image) {
                $image['url'] = $this->_mediaConfig->getPhotogalleryMediaUrl($image['img_name']);
                $image['file'] = $image['img_name'];
                $image['label'] = $image['img_label'];
                $image['tags'] = $image['tags'];
                $image['value_id'] = $image['img_id'];
                $image['photogallery_id'] = $image['photogallery_id'];
                $image['description'] = $image['img_description'];
            }
            return $this->_jsonEncoder->encode($value['images']);
        }
        return '[]';
    }

    public function getImagesValuesJson()
    {
        $values = [];
        return $this->_jsonEncoder->encode($values);
    }

    public function getImageTypes()
    {
        $imageTypes = [];
        foreach ($this->images() as $attribute) {
            $imageTypes['image'] = [
                'code' => 'image',
                'value' => $attribute['img_name'],
                'label' => $attribute['img_label'],
                'tags' => $attribute['tags'],
                'scope' => 'Store View',
                'name' => 'gallery[image]',
            ];
        }
        return $imageTypes;
    }

    public function getImageTypesJson()
    {
        return $this->_jsonEncoder->encode($this->getImageTypes());
    }
}
