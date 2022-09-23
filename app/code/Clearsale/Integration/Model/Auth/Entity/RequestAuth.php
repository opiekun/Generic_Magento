<?php
namespace Clearsale\Integration\Model\Auth\Entity;


class RequestAuth
{
	public $Login;

    function __construct(
        \Clearsale\Integration\Model\Auth\Entity\CredentialsFactory $totalAuthEntityCredentialsFactory
    ) {
        $this->Login = $totalAuthEntityCredentialsFactory;
	}
}