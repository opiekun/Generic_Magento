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
namespace FME\Photogallery\Controller\Adminhtml\Photogallery;

use Magento\Framework\App\Filesystem\DirectoryList;

class Upload extends \Magento\Backend\App\Action
{
    protected $resultRawFactory;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \FME\Photogallery\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->_imageFactory = $imageFactory;
        $this->_helper = $helper;
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('FME_Photogallery::manage_items');
    }
    public function execute()
    {
        try {
            $uploader = $this->_objectManager->create(
                'Magento\MediaStorage\Model\File\Uploader',
                ['fileId' => 'image']
            );
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
            $uploader->addValidateCallback('catalog_product_image', $imageAdapter, 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
            $config = $this->_objectManager->get('FME\Photogallery\Model\Media\ConfigPhotogallery');
            $result = $uploader->save($mediaDirectory->getAbsolutePath($config->getPhotogalleryBaseTmpMediaPath()));
            $this->_eventManager->dispatch(
                'catalog_product_gallery_upload_image_after',
                ['result' => $result, 'action' => $this]
            );
            unset($result['tmp_name']);
            unset($result['path']);
            $result['url'] = $this->_objectManager->get('FME\Photogallery\Model\Media\ConfigPhotogallery')
                ->getPhotogalleryTmpMediaUrl($result['file']);
             $fileName =  $result['file'];
            $result['file'] = $result['file'] . '.tmp';
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        $targetPath = $mediaDirectory->getAbsolutePath($config->getPhotogalleryBaseTmpMediaPath());
        if ($this->_helper->getAspectratioflag() == 1) {
            $keepRatio = true;
        } else {
            $keepRatio = false;
        }
        if ($this->_helper->getKeepframe() == 1) {
            $keepFrame = true;
        } else {
            $keepFrame = false;
        }
        $this->resizeFile($targetPath . $fileName, $keepRatio, $keepFrame, $fileName);
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));
        return $response;
    }

    public function hexToRgb($hex, $alpha = false)
    {
        $hex      = str_replace('#', '', $hex);
        $length   = strlen($hex);
        $rgb['0'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
        $rgb['1'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
        $rgb['2'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
        if ($alpha) {
            $rgb['a'] = $alpha;
        }
        return $rgb;
    }
    public function resizeFile($source, $keepRation = true, $keepFrame = true, $fileName)
    {
        if (!is_file($source) || !is_readable($source)) {
            return false;
        }
        $targetDir = $this->getThumbsPath($source);
        $width = $this->_helper->getThumbWidth();
        $height = $this->_helper->getThumbHeight();
        $bgColor = $this->_helper->getBgcolor();
        $bgColorArray = $this->hexToRgb($bgColor);
        $imageObj = $this->_imageFactory->create($source);
        $imageObj->constrainOnly(true);
        $imageObj->keepAspectRatio($keepRation);
        $imageObj->keepFrame($keepFrame);
        $imageObj->backgroundColor([intval($bgColorArray[0]),intval($bgColorArray[1]),intval($bgColorArray[2])]);
        $imageObj->resize($width, $height);
        $dest = $targetDir . '/' . pathinfo($source, PATHINFO_BASENAME);
        $imageObj->save($dest);
        if (is_file($dest)) {
            return $dest;
        }
        return false;
    }
    public function getThumbsPath($filePath = false)
    {
        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(DirectoryList::MEDIA);
        $config = $this->_objectManager->get('FME\Photogallery\Model\Media\ConfigPhotogallery');
        $mediaRootDir = $mediaDirectory->getAbsolutePath($config->getPhotogalleryBaseTmpMediaPath());
        $thumbnailDir = $mediaDirectory->getAbsolutePath($config->getPhotogalleryBaseTmpMediaPath());
        if ($filePath && strpos($filePath, $mediaRootDir) === 0) {
            $thumbnailDir .= dirname(substr($filePath, strlen($mediaRootDir)));
        }
        $thumbnailDir .= '/'."thumb";
        return $thumbnailDir;
    }
}
