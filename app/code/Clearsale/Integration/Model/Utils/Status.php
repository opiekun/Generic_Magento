<?php
namespace Clearsale\Integration\Model\Utils;

class Status
{

	public function toMagentoStatus($clearSaleStatus)
	{

		$status = '';
		switch($clearSaleStatus)
		{
		case "NVO" :
		case "AMA" : $status = "analyzing_clearsale";break;
		case "RPM" : 
		case "SUS" :
		case "FRD" :
		case "RPA" :
		case "RPP" : $status = "denied_clearsale";break;
		case "APM" :
		case "APA" : $status = "approved_clearsale";break;
		case "CAN" : $status = "canceled_clearsale";break;
			
		}
		return $status;
	}
	
	public function toClearSaleStatus($magentoStatus)
	{
		$status = "";
		
		switch($magentoStatus)
		{
			case "shipped"	:
			case "closed"	:
			case "invoiced" :
			case "complete" : $status = "APM";break;
			case "canceled" : $status = "CAN";break;
			case "fraud" : $status = "RPM";break;
		}
		
		return $status;
	}
}
	 