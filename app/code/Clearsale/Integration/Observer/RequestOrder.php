<?php
namespace Clearsale\Integration\Observer;


class RequestOrder
{
	public  $ApiKey ;
	public  $LoginToken;
	public  $Orders;
	public $AnalysisLocation;
	function __construct() {
		$this->Orders = array();
	}
}