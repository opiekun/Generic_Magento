<?php

namespace WeltPixel\Backend\Setup;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * Class UpgradeData
 * @package WeltPixel\Backend\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    const UPDATE_CRON_STRING_PATH = "weltpixel/crontab/license";

    /** @var WriterInterface */
    protected $configWriter;

    /**
     * UpgradeData constructor.
     * @param WriterInterface $configWriter
     */
    public function __construct(
        WriterInterface $configWriter
    )
    {
        $this->configWriter = $configWriter;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.1.1') < 0) {
            $cronExpression = $this->_generateCronExpression();
            $this->configWriter->save(self::UPDATE_CRON_STRING_PATH, $cronExpression);
        }

        $installer->endSetup();
    }

    /**
     * @return string
     */
    protected function _generateCronExpression()
    {
        $minute = rand(0, 59);
        $hour = rand(-2, 6);
        $dayOfWeek = rand(0, 6);
        if ($hour < 0) {
            $hour += 24;
        }
        return $minute . ' ' . $hour . ' * * ' . $dayOfWeek;
    }
}
