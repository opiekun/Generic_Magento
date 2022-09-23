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
namespace FME\Photogallery\Controller\Adminhtml;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class AbstractMassDelete extends \Magento\Backend\App\Action
{
    const REDIRECT_URL = '*/*/';
    protected $collection = 'Magento\Framework\Model\Resource\Db\Collection\AbstractCollection';
    protected $model = 'Magento\Framework\Model\AbstractModel';
    protected $cat = false;
    public function execute()
    {
        $selected = $this->getRequest()->getParam('selected');
        $excluded = $this->getRequest()->getParam('excluded');

        try {
            if (isset($excluded)) {
                if ($excluded!='false') {
                    $this->excludedDelete($excluded);
                } else {
                    $this->deleteAll();
                }
            } elseif (!empty($selected)) {
                $this->selectedDelete($selected);
            } else {
                $this->messageManager->addError(__('Please select item(s).'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath(static::REDIRECT_URL);
    }
    protected function deleteAll()
    {
        $collection = $this->_objectManager->get($this->collection);
        $this->delete($collection);
    }
    protected function excludedDelete(array $excluded)
    {
        $collection = $this->_objectManager->get($this->collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['nin' => $excluded]);
        $this->delete($collection);
    }

    protected function selectedDelete(array $selected)
    {
        $collection = $this->_objectManager->get($this->collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['in' => $selected]);
        $this->delete($collection);
    }

    protected function delete(AbstractCollection $collection)
    {
        $count = 0;
        foreach ($collection->getAllIds() as $id) {
                $model = $this->_objectManager->get($this->model);
                $this->deleteImages($id);
                $model->load($id);
                $model->delete();
                
                ++$count;
        }
        $this->setSuccessMessage($count);
        return $count;
    }

    protected function deleteImages($id)
    {
        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(DirectoryList::MEDIA);
        $config = $this->_objectManager->get('FME\Photogallery\Model\Media\ConfigPhotogallery');
        $mediaRootDir = $mediaDirectory->getAbsolutePath($config->getPhotogalleryBaseTmpMediaPath());
        $object = $this->_objectManager->create('FME\Photogallery\Model\ImgFactory');
        $coll = $object->create()->getCollection()->addFieldToFilter('photogallery_id', $id);
        foreach ($coll as $col) {
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
    }
    
    protected function setSuccessMessage($count)
    {
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $count));
    }

    public function splitImageValue($imageValue, $attr = "name")
    {
        $imArray=explode("/", $imageValue);
        $name=$imArray[count($imArray)-1];
        $path=implode("/", array_diff($imArray, [$name]));
        if ($attr=="path") {
            return $path;
        } else {
            return $name;
        }
    }

    protected function _isAllowed()
    {
        return true;
    }
}
