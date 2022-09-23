<?php
namespace Clearsale\Integration\Model\Auth\Entity;


class ResponseAuth
{
	public $Token;

    function __construct(
        \Clearsale\Integration\Model\Auth\Entity\TokenFactory $totalAuthEntityTokenFactory
    ) {
        $this->Token = $totalAuthEntityTokenFactory;
	}
}
