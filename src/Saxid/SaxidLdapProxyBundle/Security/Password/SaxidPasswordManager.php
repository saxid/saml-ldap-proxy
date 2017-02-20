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

	public function generateRandomPassword($length = 8)
	{
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*()_-=+?";
			$password = substr(str_shuffle($chars), 0, $length);
			return $password;
	}

	// MD5 now; use SSHA with random 4-character salt
	public function changePassword($password) {
				$user->uncryptPassword = $password;
				return $this->setPassword($password);
	}

	// SSHA with random 4-character salt TODO: later set PW to bcrypt!! if LDAP support this
	private function setPassword($password)
	{
			#$this->password = '{CRYPT}' . password_hash($password, PASSWORD_BCRYPT);
			#$salt = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', 4)), 0, 4);
			#$this->password = '{SSHA}' . base64_encode(sha1( $password.$salt, TRUE ) . $salt);
			$this->user->password = '{MD5}' . base64_encode(md5($password, TRUE));
			return $this;
	}
}
