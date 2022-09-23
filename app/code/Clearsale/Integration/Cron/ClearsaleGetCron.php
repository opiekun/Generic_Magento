<?php

namespace Clearsale\Integration\Cron;

use \Psr\Log\LoggerInterface;

class ClearsaleGetCron
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
      $this->logger->info('ClearsaleGetCron * / 10 start');
      $this->observer->getClearsaleOrderStatus();
  }

}