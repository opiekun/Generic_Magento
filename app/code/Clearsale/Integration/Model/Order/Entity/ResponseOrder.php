<?php
namespace Clearsale\Integration\Model\Order\Entity;

class ResponseOrder
{
	public $Orders;
	public $TransactionID;
	function __construct() {
		$this->Orders = array();
	}
}