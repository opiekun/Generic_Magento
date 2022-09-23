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

namespace Magezon\Core\Model\Eav\Attribute\Backend;

use Magento\Framework\App\Filesystem\DirectoryList;

class Image extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $rootDirectory;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     */
    public function __construct(
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->_backendUrl   = $backendUrl;
        $this->rootDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
    }

    /**
     * Validate SKU
     *
     * @param Product $object
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value    = $object->getData($attrCode);
        if ($value && !$this->isImage($value)) {
            throw new \Magento\Framework\Exception\InputException(
                __('File \'%1\' is not an image.', $value)
            );
        }
        return true;
    }

    /**
     * Simple check if file is image
     *
     * @param array|string $fileInfo - either file data from \Zend_File_Transfer or file path
     * @return boolean
     * @see \Magento\Catalog\Model\Product\Option\Type\File::_isImage
     */
    protected function isImage($fileInfo)
    {
        if (file_exists($this->rootDirectory->getAbsolutePath($fileInfo))) {
            // Maybe array with file info came in
            if (is_array($fileInfo)) {
                return strstr($fileInfo['type'], 'image/');
            }

            // File path came in - check the physical file
            if (!$this->rootDirectory->isReadable($this->rootDirectory->getRelativePath($fileInfo))) {
                return false;
            }
            $imageInfo = getimagesize($this->rootDirectory->getAbsolutePath($fileInfo));
            if (!$imageInfo) {
                return false;
            }
        }
        return true;
    }
}
