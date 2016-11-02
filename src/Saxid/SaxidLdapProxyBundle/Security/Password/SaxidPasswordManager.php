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
				return $this->setPassword($password);
	}

	// SSHA with random 4-character salt TODO: later set PW to bcrypt!! if LDAP support this
	public function setPassword($password)
	{
			#$this->password = '{CRYPT}' . password_hash($password, PASSWORD_BCRYPT);
			#$salt = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', 4)), 0, 4);
			#$this->password = '{SSHA}' . base64_encode(sha1( $password.$salt, TRUE ) . $salt);
			$this->password = '{MD5}' . base64_encode(md5($password, TRUE));
			return $this;
	}
}
