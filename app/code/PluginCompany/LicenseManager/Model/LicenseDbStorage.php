<?php
namespace PluginCompany\LicenseManager\Model;

use Magento\Framework\Flag;

class LicenseDbStorage extends Flag
{
    /**
     * Flag code
     *
     * @var string
     */
    protected $_flagCode = 'plugincompany_license';

    public function saveLicense($extensionKey, $license)
    {
        $this->init($extensionKey);
        $this->setData('flag_data', $license);
        return $this->save();
    }

    public function retrieveLicense($extensionKey)
    {
        $this->init($extensionKey);
        return $this->getData('flag_data');
    }

    public function deleteLicense($extensionKey)
    {
        $this->init($extensionKey);
        $this->setData('flag_data', '');
        return $this->save();
    }

    private function init($extensionKey)
    {
        $this->_flagCode = $extensionKey . '_license';
        $this->loadSelf();
        return $this;
    }
}