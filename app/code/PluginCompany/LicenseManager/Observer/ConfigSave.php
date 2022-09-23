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
namespace PluginCompany\LicenseManager\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class ConfigSave implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \PluginCompany\LicenseManager\Model\LicenseManager
     */
    private $licenseManager;

    /** @var \Magento\Framework\Module\Dir\Reader */
    protected $moduleReader;

    /** @var \Magento\Framework\Filesystem */
    protected $fileSystem;

    private $backendHelper;

    public function __construct(
        \PluginCompany\LicenseManager\Model\LicenseManager $licenseManager,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Backend\Helper\Data $HelperBackend
    )
    {
        $this->licenseManager = $licenseManager;
        $this->moduleReader = $moduleReader;
        $this->fileSystem = $fileSystem;
        $this->backendHelper = $HelperBackend;
        return $this;
    }

    /**
     * Execute Observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $this->request = $observer->getRequest();
        if($this->canUpdateLicenses()){
            $this->updateLicenses();
        }
    }

    private function canUpdateLicenses()
    {
        if(!stristr($this->request->getPathInfo(), 'plugincompany_licensemanager')){
            return false;
        }
        return true;
    }

    private function updateLicenses()
    {
        foreach($this->licenseManager->getProprietaryModules() as $extensionKey => $name)
        {
            if($this->isLicenseKeyInPostData($extensionKey))
            {
                $this->saveLicenseFromServer($extensionKey);
            }else{
                $this->deleteLicenseXml($extensionKey);
            }
        }
        return $this;
    }

    private function isLicenseKeyInPostData($extensionKey)
    {
        return (!empty($this->getPostLicenseKeys())
            && !empty($this->getPostLicenseKeys()[$extensionKey])
            && $this->getPostLicenseKeys()[$extensionKey]
        );
    }

    private function saveLicenseFromServer($extensionKey)
    {
        $licenseKey = $this->getLicenseKeyFromPost($extensionKey);

        $this->writeLicenseXml(
            $this->retrieveLicenseXml($licenseKey, $extensionKey),
            $extensionKey
        );

        return $this;
    }

    private function getLicenseKeyFromPost($extensionKey)
    {
        return $this->getPostLicenseKeys()[$extensionKey];
    }

    private function getPostLicenseKeys()
    {
        return $this->request->getPost('licenseKeys');
    }

    private function retrieveLicenseXml($licenseKey, $extensionKey)
    {
        $source = "https://plugin.company/licensemanager/license/retrievexml";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $source);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            [
                'license_key' => $licenseKey,
                'extension_key' => 'm2-' . $extensionKey,
                'admin_url' => $this->getAdminBaseUrl()
            ]
        );
        $data = curl_exec ($ch);
        $error = curl_error($ch);
        curl_close ($ch);

        return $data;
    }

    private function getAdminBaseUrl()
    {
        return $this->backendHelper->getHomePageUrl();
    }

    private function writeLicenseXml($xml, $extensionKey)
    {
        $this->licenseManager->writeLicenseXml($xml, $extensionKey);
        return $this;
    }

    private function deleteLicenseXml($extensionKey)
    {
        $this->licenseManager->deleteLicenseXml($extensionKey);
        return $this;
    }

    private function getLicenseXmlPath($extensionKey)
    {
        $this->licenseManager->getLicenseXmlPath($extensionKey);
    }

}
