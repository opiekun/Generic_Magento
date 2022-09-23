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
namespace PluginCompany\LicenseManager\Model\System\Message;

use Magento\Framework\Notification\MessageInterface;

/**
 * Class LicenseMessage
 */
class LicenseMessage implements MessageInterface
{

    /**
     * @var \PluginCompany\LicenseManager\Model\LicenseManager
     */
    protected $licenseManager;

    private $urlBuilder;
    private $identity;

    public function __construct
    (
        \PluginCompany\LicenseManager\Model\LicenseManager $licenseManager,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->licenseManager = $licenseManager;
        $this->urlBuilder = $urlBuilder;
    }
    /**
     * Message identity
     */
    const MESSAGE_IDENTITY = 'plugincompany_license_message';

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
        $unlicensed = $this->licenseManager->getUnlicensedModules();
        if(count($unlicensed)){
            return true;
        }
    }

    /**
     * Retrieve system message text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getText()
    {
        return __("<b>The following extension(s) installed on your system have no (valid) license key installed:</b><br>
%1<br>
Please go to the <a href='%2'>License Manager</a> and follow the steps to retrieve, configure and install your license keys.",
            implode(", ",$this->licenseManager->getUnlicensedModules()),
            $this->urlBuilder->getUrl("adminhtml/system_config/edit/section/plugincompany_licensemanager")
        );
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