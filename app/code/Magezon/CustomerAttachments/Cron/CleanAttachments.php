<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://magezon.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_CustomerAttachments
 * @copyright Copyright (C) 2018 Magezon (https://magezon.com)
 */

namespace Magezon\CustomerAttachments\Cron;

use \Magento\Framework\App\Filesystem\DirectoryList;

class CleanAttachments
{
    /**
     * @var \Magezon\CustomerAttachments\Helper\File
     */
    protected $fileHelper;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\CollectionFactory
     */
    protected $attachmentCollectionFactory;

    /**
     * @param \Magezon\CustomerAttachments\Helper\File                                      $fileHelper                  
     * @param \Magento\Framework\Filesystem                                                 $filesystem                  
     * @param \Magento\Framework\ObjectManagerInterface                                     $objectManager               
     * @param \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollectionFactory 
     */
    public function __construct(
        \Magezon\CustomerAttachments\Helper\File $fileHelper,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magezon\CustomerAttachments\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollectionFactory
    ) {
        $this->fileHelper                  = $fileHelper;
        $this->mediaDirectory              = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_objectManager              = $objectManager;
        $this->attachmentCollectionFactory = $attachmentCollectionFactory;
    }

    /**
     * Delete un-usued attachments
     *
     * @return void
     */
    public function execute()
    {   
        $this->_deleteFiles();
        $this->_deleteDirectories();
    }

    protected function _deleteFiles()
    {
        $attachCollection = $this->attachmentCollectionFactory->create();
        $dirAbsPath       = $this->mediaDirectory->getAbsolutePath($this->fileHelper->getBaseMediaPath());
        $collection       = $this->_objectManager->create(\Magento\Framework\Data\Collection\Filesystem::class);
        $collection->addTargetDir($dirAbsPath);

        foreach ($collection as $item) {
            $fileName = str_replace($dirAbsPath, '', $item->getFilename());
            $status   = false;
            foreach ($attachCollection as $_attachment) {
                if ($_attachment->getAttachmentFile() == $fileName) {
                    $status = true;
                }
            }
            if (!$status) {
                unlink($item->getFilename());
            }
        }
    }

    protected function _deleteDirectories()
    {
        $this->mediaDirectory->delete($this->fileHelper->getBaseTmpMediaPath());
        $dirAbsPath = $this->mediaDirectory->getAbsolutePath($this->fileHelper->getBaseMediaPath());
        $collection = $this->_objectManager->create(\Magento\Framework\Data\Collection\Filesystem::class);
        $collection->addTargetDir($dirAbsPath)
        ->setCollectDirs(true)
        ->setCollectFiles(false)
        ->setCollectRecursively(true);
        $items = $collection->getItems();
        krsort($items);

        foreach ($items as $dir) {
            if ($this->isDirEmpty($dir->getFilename())) {
                $fileName = str_replace($dirAbsPath, $this->fileHelper->getBaseMediaPath(), $dir->getFilename());
                $this->mediaDirectory->delete($fileName);
            }
        }
    }

    /**
     * @param  string  $dir
     * @return boolean
     */
    private function isDirEmpty($dir)
    {
        if (!is_readable($dir)) return NULL;
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != ".." && $entry != ".DS_Store") {
                return FALSE;
            }
        }
        return TRUE;
    }
}
