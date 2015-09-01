<?php

namespace Saxid\SaxidLdapProxyBundle\LdapProxy;

use Saxid\SaxidLdapProxyBundle\Security\User\SaxidUser;

/**
* Fetches data from an SAML2 IdP and passes them through to an LDAP server
*/
class SaxidLdapProxy
{
	private $debug = true;
	private $ldapHost = 'ldap://127.0.0.1';
	private $ldapPass = 'saxidsaxid';
	private $ldapConn;
	private $user;
	private $logger;
	
	function __construct($auth) {
		$this->startLogging();
		$this->createLdapBind();

		// Get user attributes
		$attributes = $auth->getAttributes();
		
		$this->logEvent("User information fetched from IdP");

		// Instantiate new SaxID User
		$this->user = new SaxidUser($attributes);

		// Check if user is from Saxon academy
		if($this->user->isFromSaxonAcademy()) {
			// Prepare user data
			$data = $this->user->createLdapDataArray();
			$this->logEvent("User data for user {$this->user} prepared");

			// Add user to LDAP
			$result = @ldap_add($this->ldapConn, $data['dn'], $data['data']);

			// Modify if LDAP entry already exists
			if($result === false && ldap_errno($this->ldapConn) == 68) {
				@ldap_modify($this->ldapConn, $data['dn'], $data['data']);
			}

			// Check for other errors
			elseif($result === false) {
				$error = $this->getLdapError("Could not add user {$this->user} to LDAP");
				$this->logEvent($error);
			}

			// No errors, ldap query successful
			else {
				$this->logEvent("User {$this->user} added to LDAP");
			}
		}

		// User is no SaxID member (no member of a Saxon academy), abort process
		else {
			$this->logEvent("User {$this->user} is not associate of a Saxon academy, aborting");
		}

		// Close LDAP connection
		ldap_close($this->ldapConn);

		// Close log file access
		fclose($this->logger);
	}

	public function getUser() {
		return $this->user;
	}

	public function getLdapData() {
		// Prepare logger and LDAP bin
		$this->startLogging();
		$this->createLdapBind();

		// Prepare fetch parameters
		$dn     = "cn={$this->user->getCommonName()},o={$this->user->getAcademyDomain()},dc=sax-id,dc=de";
		$filter = "(objectclass=*)";

		// Fetch entries
		$sr     = ldap_read($this->ldapConn, $dn, $filter);
		$entry  = ldap_get_entries($this->ldapConn, $sr);

		// Close logger and LDAP bind
		ldap_close($this->ldapConn);
		fclose($this->logger);

		// Return data
		return $entry;
	}

	private function createLdapBind() {
		// Connect to LDAP server
		$this->ldapConn = ldap_connect($this->ldapHost) or die("Could not connect to {$this->ldapHost}");
		$this->logEvent("Connection to LDAP established");

		// Enforcve LDAP v3
		ldap_set_option($this->ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);

		// Bind to LDAP
		$ldapBind = ldap_bind($this->ldapConn, "cn=admin,dc=sax-id,dc=de", $this->ldapPass);

		if($ldapBind !== false) {
			$this->logEvent("Bind successfull established");
		}

		// Bind error
		else {
			$error = $this->getLdapError("Could not bind to LDAP server");
			$this->logEvent($error);
			die();
		}
	}

	private function startLogging() {
		$date = date('Y-m-d');
		$file = "/var/log/www/saxid-ldap-proxy/{$date}.log";
		$this->logger = fopen($file, 'a');
	}

	private function logEvent($event) {
		if($this->debug) {
			$datetime = date('Y-m-d H:i:s');
			$message = "[{$datetime}] {$event}" . PHP_EOL;
			fwrite($this->logger, $message);
		}
	}

	private function getLdapError($message = null) {
		if($message === null) {
			$message = '';
		}

		$error = ldap_error($this->ldapConn);

		if($message !== '' && !empty($error)) {
			$message .= ": ";
		}

		if(!empty($error)) {
			$message .= $error;
		}

		$errno = ldap_errno($this->ldapConn);
		if(!empty($errno)) {
			$message .= " (#" . $errno . ")";
		}

		return $message;
	}
}