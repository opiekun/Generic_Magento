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
namespace PluginCompany\ProductPdf\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class ImageCompare
    extends AbstractHelper
{
    public function __construct(
        Context $context
    ) {
        parent::__construct(
            $context
        );
    }

    /**
     * Main function. Returns the hammering distance of two images' bit value.
     *
     * @param string $pathOne Path to image 1
     * @param string $pathTwo Path to image 2
     *
     * @return bool|int Hammering value on success. False on error.
     */
    public function compare($pathOne, $pathTwo) {
        $i1 = $this->createImage($pathOne);
        $i2 = $this->createImage($pathTwo);
        if (!$i1 || !$i2) {
            return false;
        }
        $i1 = $this->resizeImage($pathOne);
        $i2 = $this->resizeImage($pathTwo);
        $colorMeanOne = $this->colorMeanValue($i1);
        $colorMeanTwo = $this->colorMeanValue($i2);
        $hammeringDistance = 0;
        for ($x = 0; $x < 64; $x++) {
            if (
                $colorMeanOne[1][$x] > $colorMeanTwo[1][$x]
                || $colorMeanOne[1][$x] < $colorMeanTwo[1][$x]
            ) {
                $hammeringDistance++;
            }
        }
        return $hammeringDistance;
    }
    /**
     * Returns array with mime type and if its jpg or png. Returns false if it isn't jpg or png.
     *
     * @param string $path Path to image.
     *
     * @return array|bool
     */
    private function mimeType($path) {
        $mime = getimagesize($path);
        $return = array($mime[0],$mime[1]);

        switch ($mime['mime']) {
            case 'image/jpeg':
                $return[] = 'jpg';
                return $return;
            case 'image/png':
                $return[] = 'png';
                return $return;
            default:
                return false;
        }
    }
    /**
     * Returns image resource or false if it's not jpg or png
     *
     * @param string $path Path to image
     *
     * @return bool|resource
     */
    private function createImage($path) {
        $mime = $this->mimeType($path);

        if ($mime[2] == 'jpg') {
            return imagecreatefromjpeg ($path);
        } else if ($mime[2] == 'png') {
            return imagecreatefrompng ($path);
        } else {
            return false;
        }
    }
    /**
     * Resize the image to a 8x8 square and returns as image resource.
     *
     * @param string $path Path to image
     *
     * @return resource Image resource identifier
     */
    private function resizeImage($path) {
        $mime = $this->mimeType($path);
        $t = imagecreatetruecolor(8, 8);

        $source = $this->createImage($path);

        imagecopyresized($t, $source, 0, 0, 0, 0, 8, 8, $mime[0], $mime[1]);

        return $t;
    }
    /**
     * Returns the mean value of the colors and the list of all pixel's colors.
     *
     * @param resource $resource Image resource identifier
     *
     * @return array
     */
    private function colorMeanValue($resource) {
        $colorList = array();
        $colorSum = 0;
        for ($a = 0; $a<8; $a++) {
            for ($b = 0; $b<8; $b++) {
                $rgb = imagecolorat($resource, $a, $b);
                $colorList[] = $rgb;
                $colorSum += $rgb & 0xFF;
            }
        }

        return array($colorSum/64,$colorList);
    }

}