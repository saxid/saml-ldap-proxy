<?php

namespace Saxid\SaxidLdapProxyBundle\Security\Password;

use Saxid\SaxidLdapProxyBundle\Security\User\SaxidUser;

/**
* Represents a SaxID User
*/
class SaxidPasswordManager
{
	private $user;

	function __construct(SaxidUser $user) {
		$this->user = $user;
	}

	public function changePassword() {
		
	}
}
