<?php
namespace Clearsale\Integration\Model\Order\Entity;


class Person
{
	public $ID;      
	public $Type;
	public $Name;
	public $BirthDate;
	public $Email;
	public $LegalDocument;
	public $Gender;
	
	public $Address;
	public $Phones;

    /**
     * @var \Clearsale\Integration\Model\Order\Entity\AddressFactory
     */
    protected $totalOrderEntityAddressFactory;

    function __construct(
        \Clearsale\Integration\Model\Order\Entity\AddressFactory $totalOrderEntityAddressFactory
    ) {
        $this->totalOrderEntityAddressFactory = $totalOrderEntityAddressFactory;
		$this->Address = $this->totalOrderEntityAddressFactory->create();
		$this->Phones = array();

	}
}


