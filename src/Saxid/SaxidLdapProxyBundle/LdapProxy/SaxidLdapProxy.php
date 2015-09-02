<?php

namespace Saxid\SaxidLdapProxyBundle\LdapProxy;

use Symfony\Component\Security\Core\SecurityContext;

use Saxid\SaxidLdapProxyBundle\Security\User\SaxidUser;

/**
* Fetches data from an SAML2 IdP and passes them through to an LDAP server
*/
class SaxidLdapProxy
{
	private $debug = true;
	private $ldapHost = 'ldap://192.168.56.102';
	private $ldapPass = 'saxidsaxid';
	private $ldapConn;
	private $user;
	private $logger;
	private $status;
	
	function __construct(SecurityContext $securityContext) {
		$this->startLogging();

		$this->user = $securityContext->getToken()->getUser();

		$this->createLdapBind();

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
				$this->setStatus("Userdata of {$this->user} updated in LDAP", 'info');
			}

			// Check for other errors
			elseif($result === false) {
				$message = "Could not add user {$this->user} to LDAP";
				$this->setStatus($this->getLdapError($message), 'danger');
			}

			// No errors, ldap query successful
			else {
				$this->setStatus("User {$this->user} added to LDAP");
			}
		}

		// User is no SaxID member (no member of a Saxon academy), abort process
		else {
			$this->setStatus("User {$this->user} is not associate of a Saxon academy, aborting", 'danger');
		}

		$this->logEvent($this->getStatusMessage());

		// Close LDAP connection
		ldap_close($this->ldapConn);

		// Close log file access
		fclose($this->logger);
	}

	private function setStatus($message, $type = 'success') {
		$this->status = array('message' => $message, 'type' => $type);
	}

	public function getStatus() {
		return $this->status;
	}

	public function getStatusMessage() {
		return $this->status['message'];
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
		$this->ldapConn = \ldap_connect($this->ldapHost) or die("Could not connect to {$this->ldapHost}");
		$this->logEvent("Connection to LDAP established");

		// Enforcve LDAP v3
		ldap_set_option($this->ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);

		// Bind to LDAP
		$ldapBind = @ldap_bind($this->ldapConn, "cn=admin,dc=sax-id,dc=de", $this->ldapPass);

		if($ldapBind !== false) {
			$this->logEvent("Bind successfull established");
		}

		// Bind error
		else {
			$message = 'Could not bind to LDAP server';
			$error = $this->getLdapError($message);
			$this->logEvent($error);
			throw new \RuntimeException($message);
		}
	}

	private function startLogging() {
		$date = date('Y-m-d');

		$ldaplogdir = getenv('SAXIDLDAPPROXY_LOG_DIR');

		$file = "{$ldaplogdir}/{$date}.log";
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