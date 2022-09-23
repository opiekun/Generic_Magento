<?php

namespace Clearsale\Integration\Model\Config\Source;

use \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory;
use \Magento\Backend\Block\Template\Context;

class Orderstatuses implements \Magento\Framework\Option\ArrayInterface
{
    protected $statusCollectionFactory;
     
    public function __construct(
		Context $context,
        CollectionFactory $statusCollectionFactory
    ) {
		$this->context = $context;
        $this->statusCollectionFactory = $statusCollectionFactory;
    }
  
    public function toOptionArray()
    {
        $options = $this->statusCollectionFactory->create()->toOptionArray();        
        return $options;
    }
}