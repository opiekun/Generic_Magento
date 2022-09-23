<?php

namespace Clearsale\Integration\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\ObjectManager;

class InstallData implements InstallDataInterface
{
	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
		$setup->startSetup();
		
        $objectManager = ObjectManager::getInstance();

        $status = $objectManager->create('Magento\Sales\Model\Order\Status');
        $status->setStatus('approved_clearsale');
        $status->setLabel('Approved ClearSale');
        $status->save();
        
		$status = $objectManager->create('Magento\Sales\Model\Order\Status');
        $status->setStatus('denied_clearsale');
        $status->setLabel('Denied ClearSale');
        $status->save();

        $status = $objectManager->create('Magento\Sales\Model\Order\Status');
        $status->setStatus('canceled_clearsale');
        $status->setLabel('Canceled ClearSale');
        $status->save();

        $status = $objectManager->create('Magento\Sales\Model\Order\Status');
        $status->setStatus('analyzing_clearsale');
        $status->setLabel('Analyzing ClearSale');
        $status->save();
    }
}