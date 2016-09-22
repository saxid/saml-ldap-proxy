<?php

namespace Saxid\SaxidLdapProxyBundle\Services\SaxidServiceHPC;

use Saxid\SaxidLdapProxyBundle\Security\User\SaxidUser;

/**
* The SaxID HPC Service
*/
class SaxidServiceHPC
{
	private $user;
	private $password;
	private $oldpassword;

	function __construct(SaxidUser $user) {
		$this->user = $user;
		$this->oldpassword = $user->password;
	}

}
