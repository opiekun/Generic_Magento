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

class File extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_httpRequest;

    /**
     * @param \Magento\Framework\App\Helper\Context     $context       
     * @param \Magento\Framework\ObjectManagerInterface $objectManager 
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        parent::__construct($context);
        $this->_objectManager = $objectManager;
        $this->_httpRequest   = $context->getRequest();
    }

    public function uploadImage($type, $data, $mediaFolder, $allowedExtensions = '', $maximumSize = 0)
    {
        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA);
        $imagePath = $mediaDirectory->getAbsolutePath();
        if (isset($data[$type]['delete'])) {
            $imagePath .= isset($data[$type]['value']) ? $data[$type]['value'] : $data[$type];
            if ($imagePath != $mediaDirectory && file_exists($imagePath)) {
                unlink($imagePath);
            }
            $data[$type] = '';
        }
        $image = $this->_httpRequest->getFiles($type);
        if (isset($image['error']) && $image['error'] == 0) {
            if ($imagePath) {
                if ($imagePath != $mediaDirectory && file_exists($imagePath) && $imagePath != $mediaDirectory->getAbsolutePath()) {
                    unlink($imagePath);
                }
            }
            if ($maximumSize && $image['size'] > $maximumSize) {
                throw new \Magento\Framework\Exception\ValidatorException(__('The file is too large. Maximum upload file size: %1MB', $maximumSize/1000000));
            }
            $savePath = $mediaDirectory->getAbsolutePath($mediaFolder);
            $uploader = $this->_objectManager->create(
                'Magento\Framework\File\Uploader',
                array('fileId' => $type)
                );
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            if ($allowedExtensions) {
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            }
            $file        = $uploader->save($savePath);
            $data[$type] = $mediaFolder . '/' . $file['name'];
            $data[$type] = str_replace('//', '/', $data[$type]);
        } else if (isset($data[$type]) && is_array($data[$type])) {
            $data[$type] = $data[$type]['value'];
        }
        return $data;
    }
}
