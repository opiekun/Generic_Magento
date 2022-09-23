<?php 
namespace ClassWallet\Payment\Api;

interface OrderInterface {

	/**
	 * GET for api
	 * @param string $orderId
	 * @return string[]
	 */
	
	public function getOrder($orderId);

    /**
	 * PUT for api
	 * @param string $orderId
	 * @return string
	 */
	public function setClasswalletOrder($orderId);
}