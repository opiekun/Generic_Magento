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
namespace PluginCompany\ProductPdf\Notification;

use Magento\Framework\Notification\MessageInterface;
use Magento\Framework\UrlInterface;
use PluginCompany\ProductPdf\Setup\MpdfInstaller;

/**
 * Class MpdfLibrary
 */
class MpdfLibrary implements MessageInterface
{

    private $urlBuilder;

    public function __construct
    (
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }
    /**
     * Message identity
     */
    const MESSAGE_IDENTITY = 'plugincompany_mpdf_library_message';

    /**
     * Retrieve unique system message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return self::MESSAGE_IDENTITY;
    }

    /**
     * Check whether the system message should be shown
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return !MpdfInstaller::isMpdfInstalled();
    }

    /**
     * Retrieve system message text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getText()
    {
        return __(
            "<p><b>MPDF Library is not installed but required for the Product Page PDF extension</b></p>
             <p>Please <a href='{$this->getInfoUrl()}'>click here</a> for more information and steps to install the required library</p>"
        );
    }

    private function getInfoUrl()
    {
        return $this->urlBuilder->getUrl("productpagepdf/mpdf/index");
    }

    /**
     * Retrieve system message severity
     * Possible default system message types:
     * - MessageInterface::SEVERITY_CRITICAL
     * - MessageInterface::SEVERITY_MAJOR
     * - MessageInterface::SEVERITY_MINOR
     * - MessageInterface::SEVERITY_NOTICE
     *
     * @return int
     */
    public function getSeverity()
    {
        return self::SEVERITY_CRITICAL;
    }
}