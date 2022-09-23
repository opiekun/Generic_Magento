<?php
namespace WeltPixel\OwlCarouselSlider\Cron;

use \Psr\Log\LoggerInterface;

class CronFile
{
    protected $logger;
    protected $_objectManager;

    /**
     * CronFile constructor.
     * @param LoggerInterface $logger
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->logger = $logger;
        $this->_objectManager = $objectManager;
    }

    /**
     * Write to system.log
     *
     * @return void
     */

    public function execute()
    {
        $this->logger->info('Executed OwlCarouselSlider\Cron\CronFile');
        try {
            $this->_objectManager->create('Magento\Sales\Model\ResourceModel\Report\Bestsellers')->aggregate();
            $this->logger->info('Refreshed Bestsellers lifetime statistics.');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->info($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->info(('Couldn\'t refresh Bestsellers lifetime statistics.'));
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }

        return;
    }

}