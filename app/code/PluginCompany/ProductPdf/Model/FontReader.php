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

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class FontReader
{

    /** @var \Magento\Framework\Filesystem\Io\File  */
    private $io;

    /** @var FontDirIO  */
    private $fontDirIO;

    /** @var Registry */
    private $registry;

    public function __construct(
        Context $context,
        FontDirIO $fontDirIO,
        Registry $registry
    ) {
        $this->fontDirIO = $fontDirIO;
        $this->io = $this->io = $fontDirIO->getIo();
        $this->registry = $registry;
        return $this;
    }

    public function getAdditionalFontsForMpdf()
    {
        $fonts = array();
        foreach($this->fontDirIO->readFontDir() as $fontDir){
            $this->io->open(array('path' => $fontDir['id']));
            $fontKey = $fontDir['text'];
            $fonts[$fontKey] = array(
                'R' => $fontKey . '/' . $this->getRegularFont(),
                'B' => $fontKey . '/' . $this->getBoldFont(),
                'I' => $fontKey . '/' . $this->getItalicFont(),
                'BI' => $fontKey . '/' . $this->getBoldItalicFont()
            );
        }
        return $fonts;
    }

    private function getRegularFont()
    {
        return $this->getFontFile('-Regular.ttf');
    }

    private function getBoldFont()
    {
        return $this->getFontFile('-Bold.ttf');
    }

    private function getItalicFont()
    {
        if($font = $this->getFontFile('-Italic.ttf')){
            return $font;
        }
        return $this->getFontFile('-Regular.ttf');
    }

    private function getBoldItalicFont()
    {
        if($font = $this->getFontFile('-BoldItalic.ttf')){
            return $font;
        }
        return $this->getFontFile('-Bold.ttf');
    }

    private function getFontFile($suffix)
    {
        foreach($this->io->ls() as $file) {
            if(stristr($file['text'], $suffix)){
                return $file['text'];
            }
        }
        return false;
    }

    public function getFontImageData()
    {
        if($this->registry->registry('font_image_data')){
            return $this->registry->registry('font_image_data');
        }
        $images = array();
        foreach($this->fontDirIO->readFontDir() as $fontDir) {
            $imageData = $this->generateImageData($fontDir);
            $images[$imageData['identifier']] = $imageData;
        }
        ksort($images);
        $this->registry->register('font_image_data', $images);
        return $images;
    }

    private function generateImageData($fontDir)
    {
        $this->io->open(array('path' => $fontDir['id']));
        return array(
            'identifier' => $fontDir['text'],
            'file' => $this->getImageFontFile($fontDir),
            'title' => $this->getFontTitle($fontDir),
            'image_base64' => $this->generateImage($fontDir)
        );
    }

    private function getImageFontFile($fontDir)
    {
        return $fontDir['id'] . DIRECTORY_SEPARATOR . $this->getRegularFont();
    }

    private function generateImage($fontDir)
    {
        $image = $this->generateBaseImage();
        $this->addText($image, $fontDir);
        return $this->convertBase64Image($image);
    }

    private function addText($image, $fontDir)
    {
        $grey = imagecolorallocate($image, 66, 66, 66);
        imagettftext ( $image , 18, 0, 0 , 33 , $grey, $this->getImageFontFile($fontDir), $this->getFontTitle($fontDir));
        return $this;
    }

    private function convertBase64Image($image)
    {
        ob_start();
        imagepng($image);
        $buffer = ob_get_clean();
        return base64_encode($buffer);
    }

    private function generateBaseImage()
    {
        $image = imagecreatetruecolor(270, 50);
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $white);
        return $image;
    }

    private function getFontTitle($fontDir)
    {
        $metadata = $this->io->read($fontDir['id'] . '/METADATA.pb');
        preg_match('/"(.*?)"/', $metadata, $matches);
        return isset($matches[1]) ? $matches[1] : FALSE;
    }

}
