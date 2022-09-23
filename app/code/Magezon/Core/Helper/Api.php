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

class Api extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     * @param \Magento\Framework\App\Helper\Context $context    
     * @param \Magento\Framework\Filesystem         $fileSystem 
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $fileSystem
    ) {
        parent::__construct($context);
        $this->fileSystem = $fileSystem;
    }

    public function imagePreprocessing($fileContent, $saveFolder, $enableFilesDispersion = false)
    {
        if (strpos($fileContent, 'base64') === false) {
            return $fileContent;
        }

        $mediaDirectory    = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
        if (is_array($fileContent)) {
            $imageData = explode("base64,", $fileContent['base64_encoded_data'])[1];
        } else {
            $imageData = explode("base64,", $fileContent)[1];
        }
        
        $imageData         = base64_decode($imageData);
        $destinationFolder = $mediaDirectory->getAbsolutePath($saveFolder) . '/';
        if (!file_exists($destinationFolder)) {
            mkdir($destinationFolder, 0777, true);
        }

        if (is_array($fileContent)) {
            if (!isset($fileContent['name'])) {
                $name = uniqid();
            } else {
                $name = $fileContent['name'];
            }
        } else {
            $name = uniqid();
        }

        if (strpos($name, '.') === false) {
            $name .= '.jpg';
        }

        $actual_name   = pathinfo($destinationFolder . $name,PATHINFO_FILENAME);
        $original_name = $actual_name;
        $extension     = pathinfo($destinationFolder . $name, PATHINFO_EXTENSION);
        $i             = 1;

        if ($enableFilesDispersion) {
            if ($this->endsWith($destinationFolder, "/")) {
                $destinationFolder = substr($destinationFolder, 0, -1);
            }
            $dispertionPath    = $this->getDispretionPath($name) . '/';
            $destinationFolder .=  $dispertionPath;
            $this->_createDestinationFolder($destinationFolder);
        }

        while(file_exists($destinationFolder . $actual_name . '.' . $extension))
        {
            $actual_name = (string) $original_name . $i;
            $name        = $actual_name . "." . $extension;
            $i++;
        }

        $success  = file_put_contents($destinationFolder . $name, $imageData);
        if ($success) {
            if ($enableFilesDispersion) {
                return $dispertionPath . $name;
            }
            return $name;
        }
        return null;
    }

    /**
     * Get dispertion path
     *
     * @param string $fileName
     * @return string
     */
    public static function getDispretionPath($fileName)
    {
        $char = 0;
        $dispertionPath = '';
        while ($char < 2 && $char < strlen($fileName)) {
            if (empty($dispertionPath)) {
                $dispertionPath = '/' . ('.' == $fileName[$char] ? '_' : $fileName[$char]);
            } else {
                $dispertionPath = self::_addDirSeparator(
                    $dispertionPath
                ) . ('.' == $fileName[$char] ? '_' : $fileName[$char]);
            }
            $char++;
        }
        return $dispertionPath;
    }

    /**
     * Add directory separator
     *
     * @param string $dir
     * @return string
     */
    protected static function _addDirSeparator($dir)
    {
        if (substr($dir, -1) != '/') {
            $dir .= '/';
        }
        return $dir;
    }

     /**
     * Create destination folder
     *
     * @param string $destinationFolder
     * @return \Magento\Framework\File\Uploader
     * @throws \Exception
     */
    private function _createDestinationFolder($destinationFolder)
    {
        if (!$destinationFolder) {
            return $this;
        }

        if (substr($destinationFolder, -1) == '/') {
            $destinationFolder = substr($destinationFolder, 0, -1);
        }

        if (!(is_dir($destinationFolder)
            || mkdir($destinationFolder, 0777, true)
        )) {
            throw new \Exception("Unable to create directory '{$destinationFolder}'.");
        }
        return $this;
    }

    public function endsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return $length === 0 || 
        (substr($haystack, -$length) === $needle);
    }
}
