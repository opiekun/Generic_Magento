<?php

namespace WeltPixel\OwlCarouselSlider\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * FrontendOptionsEditActionControllerSaveObserver observer
 */
class BestSellEditActionControllerSaveObserver implements ObserverInterface
{
    /**
     * Cron string path
     */
    const CRON_STRING_PATH = 'crontab/default/jobs/bestsell_cron_setup/schedule/cron_expr';

    /**
     * Cron model path
     */
    const CRON_MODEL_PATH = 'crontab/default/jobs/bestsell_cron_setup/run/model';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Store collection
     * storeId => \Magento\Store\Model\Store
     *
     * @var array
     */
    protected $_storeCollection;

    /**
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    protected $_configValueFactory;

    /**
     * @var string
     */
    protected $_runModelPath = '';

    /**
     * BestSellEditActionControllerSaveObserver constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ValueFactory $configValueFactory
     * @param string $runModelPath
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        $runModelPath = ''
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_configValueFactory = $configValueFactory;
        $this->_runModelPath = $runModelPath;
    }

    /**
     * Save cron expression
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        if ($event instanceof \Magento\Framework\Event) {
            if ($event->getName() == 'admin_system_config_changed_section_weltpixel_owl_carousel_config') {
                $cronSettingsArr = [];
                $this->_storeCollection = $this->_storeManager->getStores();
                foreach ($this->_storeCollection as $store) {
                    $bestSellCarouselisEnabled = $this->_scopeConfig->getValue(
                        'weltpixel_owl_carousel_config/bestsell_products/status',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $store->getId()
                    );
                    if ($bestSellCarouselisEnabled) {
                        $cronSettings = $this->_getCronSettings($store->getId());
                        $cronSettingsArr[key($cronSettings)] = $cronSettings;
                    }

                }

                if ($cronSettingsArr) {
                    ksort($cronSettingsArr);
                    $highestPriority = reset($cronSettingsArr);
                    $cronExprString = array_shift($highestPriority);

                    $this->_saveCronExpression($cronExprString);
                }
            }
        }

        return $this;
    }

    /**
     * @param $storeId
     * @return array
     */
    private function _getCronSettings($storeId)
    {
        $period = $this->_scopeConfig->getValue(
            'weltpixel_owl_carousel_config/bestsell_products/period',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $cronSettings = [];
        switch ($period) {
            case 'last_day':
                // every day at 00:00
                $cronSettings[0] = '0 0 * * *';
                break;
            case 'last_week':
                $firstDayOfWeek = $this->_scopeConfig->getValue(
                    'general/locale/firstday',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $storeId
                );
                // every first day of the week at 00:00
                $cronSettings[1] = '0 0 * * ' . $firstDayOfWeek;
                break;
            case 'last_month':
                // every first day of the month at 00:00
                $cronSettings[2] = '0 0 1 * *';
                break;
            default:
                // every first day of the year at 00:00
                $cronSettings[3] = '0 0 1 1 *';
                break;
        }

        return $cronSettings;
    }

    /**
     * @param $cronExprString
     * @return $this
     * @throws \Exception
     */
    private function _saveCronExpression($cronExprString)
    {
        try {
            $this->_configValueFactory->create()->load(
                self::CRON_STRING_PATH,
                'path'
            )->setValue(
                $cronExprString
            )->setPath(
                self::CRON_STRING_PATH
            )->save();
            $this->_configValueFactory->create()->load(
                self::CRON_MODEL_PATH,
                'path'
            )->setValue(
                $this->_runModelPath
            )->setPath(
                self::CRON_MODEL_PATH
            )->save();
        } catch (\Exception $e) {
            throw new \Exception(__('We can\'t save the cron expression.'));
        }

        return $this;
    }
}
