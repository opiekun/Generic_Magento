<?php
/**
 * Created by:  Milan Simek
 * Company:     Plugin Company
 *
 * LICENSE: http://plugin.company/docs/magento-extensions/magento-extension-license-agreement
 *
 * YOU WILL ALSO FIND A PDF COPY OF THE LICENSE IN THE DOWNLOADED ZIP FILE
 *
 * FOR QUESTIONS AND SUPPORT
 * PLEASE DON'T HESITATE TO CONTACT US AT:
 *
 * SUPPORT@PLUGIN.COMPANY
 */
namespace PluginCompany\ProductPdf\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\FileFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Module\Dir\Reader;

class FontDirIO
{
    /** @var FileFactory */
    private $ioFileFactory;

    /** @var \Magento\Framework\Filesystem\Io\File  */
    private $io;

    /** @var Reader */
    private $moduleDirReader;
    /**
     * @var DirectoryList
     */
    private $directoryList;

    public function __construct(
        Context $context,
        FileFactory $ioFileFactory,
        Reader $moduleDirReader,
        DirectoryList $directoryList
    )
    {
        $this->ioFileFactory = $ioFileFactory;
        $this->io = $this->ioFileFactory->create();
        $this->moduleDirReader = $moduleDirReader;
        $this->directoryList = $directoryList;
    }

    public function readFontDir()
    {
        $this->io->open(array('path' => $this->getGoogleFontDir()));
        return $this->io->ls();
    }

    public function doesFontDirExist()
    {
        return is_dir($this->getGoogleFontDir());
    }

    public function getGoogleFontDir()
    {
        return $this->getProductPdfMediaDir() . '/googlefonts';
    }


    public function getProductPdfMediaDir()
    {
        $this->createProductPdfMediaDirIfNotExists();
        return $this->getProductPdfMediaDirPath();
    }

    private function createProductPdfMediaDirIfNotExists()
    {
        if(!is_dir($this->getProductPdfMediaDirPath())) {
            mkdir($this->getProductPdfMediaDirPath(), 0777, true);
        }
        return $this;
    }

    private function getProductPdfMediaDirPath()
    {
        return $this->getMediaDir() . '/plugincompany/productpdf/';
    }


    private function getMediaDir()
    {
        return $this->directoryList->getPath('media');
    }


    /**
     * @return \Magento\Framework\Filesystem\Io\File
     */
    public function getIo()
    {
        return $this->io;
    }

    /**
     * @return Reader
     */
    public function getModuleDirReader()
    {
        return $this->moduleDirReader;
    }



}

