<?php

namespace Ecommerce121\Filemanager\Model\Wysiwyg;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Wysiwyg\ConfigInterface;

/**
 * Class ResponsiveFileManager
 * @package Ecommerce121\Filemanager\Model\Wysiwyg
 */
class ResponsiveFileManager
{
    const PLUGIN_NAME = 'filemanager';
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var SessionManagerInterface
     */
    protected $_coreSession;

    /**
     * ResponsiveFileManager constructor.
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        SessionManagerInterface $coreSession
    ) {
        $this->storeManager = $storeManager;
        $this->_coreSession = $coreSession;

    }

    /**
     * @return string
     */
    public function getSignature() : string
    {
        $this->_coreSession->start();
        $sessionId = md5($this->_coreSession->getSessionId());
        $signature = hash('sha256', self::PLUGIN_NAME . $sessionId);
        setcookie(self::PLUGIN_NAME, $sessionId, time() + 3600 * 24,'/');
        $this->_coreSession->setSignature($signature);
        return $signature;
    }


    /**
     * @param ConfigInterface $config
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getPluginSettings(ConfigInterface $config) : array
    {
        $settings = $config->getData('settings');
        $settings['image_advtab'] =  false;
        $settings['filemanager_title'] = "File Manager";
        $settings['external_filemanager_path'] = $this->getUrlSource();
        $settings['external_plugins'] = [self::PLUGIN_NAME => $this->getUrlPluginSource()];
        $settings['filemanager_access_key'] = $this->getSignature();

        return ['settings' => $settings];
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    private function getUrlSource(): string
    {
        return sprintf('%spub/%s',
            $this->storeManager->getStore()->getBaseUrl(),
            self::PLUGIN_NAME);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    private function getUrlPluginSource(): string
    {
        return sprintf('%spub/%s/plugin.js',
            $this->storeManager->getStore()->getBaseUrl(),
            self::PLUGIN_NAME);
    }

}
