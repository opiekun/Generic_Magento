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
namespace PluginCompany\ProductPdf\Setup;

use Magento\Framework\Filesystem\Io\File;
use PluginCompany\ProductPdf\Model\FontDirIO;

class FontDownloader
{
    const FONT_PACKAGE_URL = 'https://codeload.github.com/PluginCompany/product-pdf-fonts-mirror/zip/1.0.1';

    /** @var File  */
    private $io;
    /** @var FontDirIO  */
    private $fontDirIO;

    public function __construct(
        FontDirIO $fontDirIO
    ) {
        $this->fontDirIO = $fontDirIO;
        $this->io = $this->io = $fontDirIO->getIo();
    }

    public function installIfNotAvailable()
    {
        if($this->fontDirIO->doesFontDirExist()){
            return $this;
        }
        return $this->execute();
    }

    public function execute()
    {
        $this
            ->removeOldFiles()
            ->downloadPackage()
            ->extractPackage()
            ->renameFolder()
            ->cleanUp()
        ;
        return $this;
    }

    private function removeOldFiles()
    {
        if(is_dir($this->getFontDir())){
            $this->io->rmdir(
                $this->getFontDir(),
                true
            );
        }
        if(is_file($this->getFontPackagePath())) {
            $this->io->rm($this->getFontPackagePath());
        }
        $this->removeTempFontDir();
        return $this;
    }

    private function downloadPackage()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::FONT_PACKAGE_URL);
        $fp = fopen($this->getFontPackagePath(), 'w');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_exec ($ch);
        curl_close ($ch);
        fclose($fp);
        return $this;
    }

    private function getFontPackagePath()
    {
        return $this->getProductPdfMediaDir() . 'fonts.zip';
    }

    private function getProductPdfMediaDir()
    {
        return $this->fontDirIO->getProductPdfMediaDir();
    }

    private function getFontDir()
    {
        return $this->fontDirIO->getGoogleFontDir();
    }

    private function getExtractedPackageDir()
    {
        return $this->getProductPdfMediaDir() . '/product-pdf-fonts-mirror-1.0.1';
    }

    private function extractPackage()
    {
        $zip = new \ZipArchive();
        $zip->open($this->getFontPackagePath());
        $zip->extractTo($this->getProductPdfMediaDir());
        $zip->close();
        return $this;
    }

    private function renameFolder()
    {
        $this->io->mv(
            $this->getExtractedPackageDir() . '/googlefonts',
            $this->fontDirIO->getGoogleFontDir()
        );
        return $this;
    }

    private function cleanUp()
    {
        $this
            ->removePackage()
            ->removeTempFontDir();
        ;

        return $this;
    }

    private function removePackage()
    {
        $this->io->rm(
            $this->getFontPackagePath()
        );
        return $this;
    }

    private function removeTempFontDir()
    {
        if(!is_dir($this->getExtractedPackageDir())){
            return $this;
        }
       $this->io->rmdir(
            $this->getExtractedPackageDir(),
            true
        );
        return $this;
    }

}