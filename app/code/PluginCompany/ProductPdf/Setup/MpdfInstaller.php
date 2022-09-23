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

use PluginCompany\ProductPdf\Setup\Composer\ComposerRequire;

class MpdfInstaller
{
    /**
     * @var ComposerRequire
     */
    private $composerRequire;

    public function __construct(
        ComposerRequire $composerRequire
    ) {
        $this->composerRequire = $composerRequire;
    }

    public function runInstall()
    {
        ini_set('memory_limit', -1);
        $this->composerRequire->requirePackage(['mpdf/mpdf']);
        $this->composerRequire->requirePackage(['mpdf/qrcode']);
        return $this;
    }

    static public function isMpdfInstalled()
    {
        if(!class_exists('\\Mpdf\\Mpdf')){
            return false;
        }
        if(!class_exists('\\Mpdf\\QrCode\\QrCode')){
            return false;
        }
        return true;
    }
}