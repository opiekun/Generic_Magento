<?php
namespace Clearsale\Integration\Model\Order\Entity;

class Order
{
	public $ID;
	public $Date;
	public $Email;       
	public $TotalItems;     
	public $TotalOrder; 
	public $TotalShipping;
	public $Currency;     
	public $Payments;        
	public $BillingData;      
	public $ShippingData;      
	public $Items;     
	public $CustomFields;     
	public $SessionID;
	public $IP;
	public $Reanalysis;

    /**
     * @var \Clearsale\Integration\Model\Order\Entity\PersonFactory
     */
    protected $totalOrderEntityPersonFactory;

    function __construct(
        \Clearsale\Integration\Model\Order\Entity\PersonFactory $totalOrderEntityPersonFactory
    ) {
        $this->totalOrderEntityPersonFactory = $totalOrderEntityPersonFactory;
		$this->ShippingData = $this->totalOrderEntityPersonFactory->create();
		$this->BillingData = $this->totalOrderEntityPersonFactory->create();
		$this->Items = array();
		$this->Reanalysis = false;
	}
}


