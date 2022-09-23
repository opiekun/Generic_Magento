<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Booking
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Booking\Model\Config\Backend;

/**
 * Class Banner
 * @package Ced\Booking\Model\Config\Backend
 */
class Banner extends \Magento\Config\Model\Config\Backend\File
{
    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $filevalue = $this->getValue();
        $tmpName = $this->_requestData->getTmpName($this->getPath());
        $file = [];
        if ($tmpName) {
            $file['tmp_name'] = $tmpName;
            $file['name'] = $this->_requestData->getName($this->getPath());
        } elseif (!empty($filevalue['tmp_name'])) {
            $file['tmp_name'] = $filevalue['tmp_name'];
            $file['name'] = $filevalue['name'];
        }
        if (isset($file['tmp_name'])) {

            $uploadDir = $this->_getUploadDir();
            try {
                $csUploader = $this->_uploaderFactory->create(['fileId' => $file]);
                $csUploader->setAllowedExtensions($this->_getAllowedExtensions());
                $csUploader->setAllowRenameFiles(true);
                $csUploader->addValidateCallback('size', $this, 'validateMaxSize');
                $result = $csUploader->save($uploadDir);

            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
            }
            $csFileName = $result['file'];
            if ($csFileName) {
                if ($this->_addWhetherScopeInfo()) {
                    $csFileName = $this->_prependScopeInfo($csFileName);
                }
                $this->setValue($csFileName);
            }
        } else {
            if (is_array($filevalue) && !empty($filevalue['delete'])) {
                $this->setValue('');
            } else {
                $this->unsValue();
            }
        }
        return $this;
    }

    /**
     * Getter for allowed extensions of uploaded files
     *
     * @return array
     */
    protected function _getAllowedExtensions()
    {
        return ['png', 'jpg', 'jpeg'];
    }
}
