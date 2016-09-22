<?php

namespace Saxid\SaxidLdapProxyBundle\Security\Password;

use Saxid\SaxidLdapProxyBundle\Security\User\SaxidUser;

/**
* Represents a SaxID User
*/
class SaxidPasswordManager
{
	private $user;
	private $oldpassword;

	function __construct(SaxidUser $user) {
		$this->user = $user;

		$this->oldpassword = $user->password;
	}

	// MD5 now; use SSHA with random 4-character salt
	public function changePassword($password) {
				$user->uncryptPassword = $password;
				return $user->setPassword($password);
	}
}
