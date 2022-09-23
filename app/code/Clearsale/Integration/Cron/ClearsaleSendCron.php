<?php

namespace Clearsale\Integration\Cron;

use \Psr\Log\LoggerInterface;

class ClearsaleSendCron
{
  protected $logger;
  protected $observer;

  public function __construct(
      LoggerInterface $logger,
      \Clearsale\Integration\Observer\ClearsaleObserver $observer
  ) {
       
       $this->observer = $observer;
       $this->logger = $logger;
  }

  public function execute()
  {
      $this->logger->info('ClearsaleSendCron * / 10 start');
      $this->observer->sendPendingOrders();
  }

}