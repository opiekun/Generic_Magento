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

class Delete extends \FME\Photogallery\Controller\Adminhtml\Photogallery
{
   
    public function execute()
    {
        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(DirectoryList::MEDIA);
        $config = $this->_objectManager->get('FME\Photogallery\Model\Media\ConfigPhotogallery');
        $mediaRootDir = $mediaDirectory->getAbsolutePath($config->getPhotogalleryBaseTmpMediaPath());
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = $this->_objectManager->create('FME\Photogallery\Model\Photogallery');
                $object = $this->_objectManager->create('FME\Photogallery\Model\ImgFactory');
                $collection = $object->create()->getCollection()
                ->addFieldToFilter('photogallery_id', $this->getRequest()->getParam('id'));
                foreach ($collection as $col) {
                    $file_name = $col->getImgName();
                    $imgPath=  $this->splitImageValue($file_name, "path");
                    $imgName=  $this->splitImageValue($file_name, "name");
                    $file_path = $mediaRootDir . $file_name;
                    $thumb_path = $mediaRootDir .$imgPath. DIRECTORY_SEPARATOR.'thumb'.DIRECTORY_SEPARATOR.$imgName;
                    if ($file_path) {
                        unlink($file_path);
                        unlink($thumb_path);
                    }
                }
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                $this->messageManager->addSuccess(__('Gallery was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }
        $this->_redirect('*/*/');
    }
    protected function _isAllowed()
    {
        return true;
    }
}
