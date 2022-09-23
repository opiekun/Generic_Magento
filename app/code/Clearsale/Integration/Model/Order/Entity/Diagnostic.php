<?php
namespace Clearsale\Integration\Model\Order\Entity;


class Diagnostic
{
	public $order_id;
	public $clearsale_status;
	public $score;
	public $diagnostics;
	public $dt_sent;
	public $dt_update;
}

