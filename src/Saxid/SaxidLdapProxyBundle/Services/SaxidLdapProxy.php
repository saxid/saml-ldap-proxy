<?php

namespace Saxid\SaxidLdapProxyBundle\Services;

use Saxid\SaxidLdapProxyBundle\Security\User\LdapUser;
use Symfony\Bridge\Monolog\Logger;

/**
 * Fetches data from an SAML2 IdP and passes them through to an LDAP server
 * Authors: Norman Walther (ZIH), Jan Frömberg (ZIH)
 * Date: 2016 - 2017
 */
class SaxidLdapProxy
{
    //Variables
    //LDAP
    private $ldapHost;
    private $ldapPort;
    private $ldapUser;
    private $ldapPassword;
    private $ldapConnection;
    private $baseDN;
    //Other
    private $connected = false;
    private $debug = false;
    //private $mylogger;
    private $status;

    /**
     * Creates LDAP access object
     *
     * @param string $ldap_host The ldap host address
     * @param string $ldap_port The ldap port
     * @param string $ldap_user The ldap user to bind
     * @param string $ldap_pass The ldap user password
     * @param string $baseDN The baseDN for search
     */
    public function __construct($ldap_host, $ldap_port, $ldap_user, $ldap_pass, $baseDN, Logger $logger)
    {
        $this->ldapHost = $ldap_host;
        $this->ldapPort = $ldap_port;
        $this->ldapUser = $ldap_user;
        $this->ldapPassword = $ldap_pass;
        $this->baseDN = $baseDN;
        $this->logger = $logger;

        //Start logger
        //$this->startLogging();
    }

    /**
     * Connect to LDAP and tries anonymous bind to check if connection is successful
     *
     */
    public function connect()
    {
        //Connection to LDAP
        //ldap_connect always has a return value, even if LDAP is not reachable
        $this->ldapConnection = ldap_connect($this->ldapHost);

        //Set options
        ldap_set_option($this->ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->ldapConnection, LDAP_OPT_REFERRALS, 0);
        //ldap_set_option($this->ldapConnection, LDAP_OPT_DEBUG_LEVEL, 7);

        //StartTLS
        ldap_start_tls($this->ldapConnection);

        //Check trough anonymous bind if LDAP is reachable
        if (ldap_bind($this->ldapConnection)) {
            $message = "connect - Connection to LDAP-Server (" . $this->ldapHost . ") successfully established.";
            $this->logger->info($message);
            //$this->setStatus($message, LOGLEVEL::INFO);
        } else {
            $errorMessage = $this->getLdapError("connect - Error: Connection to LDAP-Server (" . $this->ldapHost . ") could not be established.");
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            $this->connected = false;
            return false;
        }

        //Authentication bind to access data (anonymous cant read)
        if (ldap_bind($this->ldapConnection, $this->ldapUser, $this->ldapPassword)) {
            $message = "connect - Bind successfull.";
            $this->logger->info($message);
            //$this->setStatus($message, LOGLEVEL::INFO);
            $this->connected = true;
        } else {
            $errorMessage = $this->getLdapError("connect - Error: Bind failed.");
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            $this->connected = false;
            return false;
        }
        return true;
    }

    /**
     * Gets all LDAP Users
     * like this:
     *     0 => array:3 [▼
     *  "edupersonprincipalname" => array:1 [▶]
     *  0 => "edupersonprincipalname"
     *  "dn" => "cn=norman,o=tu-dresden.de,dc=sax-id,dc=de"
     *]
     *
     * will be transformed w/o count -> rCountRemover
     *
     * @return array Return an array with all ldap users and thier ePPNs
     */
    public function getAllUsers()
    {

      if (!$this->connected)
      {
          //$this->setStatus("getAllUser failed! - no connection to LDAP", LOGLEVEL::ERROR);
          return;
      }

      $filter = "(|(objectclass=eduPerson))";
      $justthese = array("eduPersonPrincipalName");

      $searchres = ldap_search($this->ldapConnection, $this->baseDN, $filter, $justthese);

      $ePPNs = ldap_get_entries($this->ldapConnection, $searchres);

      //$this->setStatus($ePPNs["count"]." Entries fround.", LOGLEVEL::ERROR);

      return $this->rCountRemover($ePPNs);
    }

    /**
     * Check if user already exists in LDAP
     *
     * @param string $seachParam Search parameter in following form: 'uid=norman'
     * @return bool Return object if user was found or false when not
     */
    public function userExist($seachParam) 
    {
        if (!$this->connected) 
        {
            $errorMessage = "userExist - Error: No valid LDAP-Connection.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return;
        }

        //returnFilter for datareduction
        $returnFilter = array("dn");

        return $this->getUserData($seachParam, $returnFilter);
    }

    /**
     * Check if organization exists in LDAP
     *
     * @param string $organizationDN DN of the organization
     * @return bool Return True if exists ir false when not
     */
    public function existsOrganization($organizationDN) 
    {
        if (!$this->connected) 
        {
            $errorMessage = "existsOrganization - Error: No valid LDAP-Connection.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return;
        }

        $filter = "objectClass=organization";

        if (@ldap_search($this->ldapConnection, $organizationDN, $filter) == false) 
        {
            $message = "existsOrganization - Organization '" . $organizationDN . "' doesn't exist.";
            $this->logger->info($message);
            //$this->setStatus($message, LOGLEVEL::INFO);
            return false;
        } else {
            $message = "existsOrganization - Organization '" . $organizationDN . "' exist.";
            $this->logger->info($message);
            //$this->setStatus($message, LOGLEVEL::INFO);
            return true;
        }
    }

    /**
     * Adds a user to LDAP
     *
     * @param string $dname The distinguished name for the new user
     * @param array $data Data array with userdata
     * @return bool True if successfully or false when not
     */
    public function addLDAPObject($dname, $data) 
    {
        if (!$this->connected) 
        {
            $errorMessage = "addLDAPObject - Error: No valid LDAP-Connection.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return;
        }

        //Adding the object
        if (ldap_add($this->ldapConnection, $dname, $data)) {
            $message = "addLDAPObject - Object '" . $dname . "' successfully added.";
            $this->logger->info($message);
            //$this->setStatus($message, LOGLEVEL::INFO);
            return true;
        } else {
            $errorMessage = $this->getLdapError("addLDAPObject - Error: Failed to add object.");
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }
    }

    /**
     * Modifys a user in LDAP
     *
     * @param string $dname The distinguished name for the user
     * @param array $data Data array with userdata
     * @return bool True if successfully or false when not
     */
    public function modifyLDAPObject($dname, $data) 
    {
        if (!$this->connected) 
        {
            $errorMessage = "modifyLDAPObject - Error: No valid LDAP-Connection.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return;
        }

        //Modify the object
        if (ldap_modify($this->ldapConnection, $dname, $data)) 
        {
            $message = "modifyLDAPObject - Object '" . $dname . "' successfully updated.";
            $this->setStatus('Deine Attribute wurden erfolgreich aktualisiert. Details findest du unter Mein Konto im Menu oben.',
            LOGLEVEL::INFO);
            $this->logger->info($message);
            //$this->setStatus($message, LOGLEVEL::INFO);
            return true;
        } else {
            $errorMessage = $this->getLdapError("modifyLDAPObject - Error: Update of object failed.");
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }
    }

    /**
     * Deletes a user in LDAP
     *
     * @param string $dname The distinguished name for the user
     * @return bool True if successfully or false when not
     */
    public function deleteLDAPObject($dname) 
    {
        if (!$this->connected) 
        {
            $errorMessage = "deleteLDAPObject - Error: No valid LDAP-Connection.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return;
        }

        //Deleting the object
        if (ldap_delete($this->ldapConnection, $dname)) 
        {
            $message = "deleteLDAPObject - Object '" . $dname . "' successfully deleted.";
            $this->logger->info($message);
            //$this->setStatus($message, LOGLEVEL::INFO);
            return true;
        } else {
            $errorMessage = $this->getLdapError("deleteLDAPObject - Error: Delete of '" . $dname . "' failed.");
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }
    }

    /**
     * Gets attributes of a user
     *
     * @param string $seachParam search paramter in following form: 'uid=tud_norman'
     * @param array $retAttrFilter Filters the return values, default everything is shown. Example: array("dn, sn, mail, ...")
     *
     * @return array Return an array with userdata of a ldapuser
     */
    public function getUserData($seachParam, $retAttrFilter = array("*")) 
    {
        if (!$this->connected) 
        {
            $errorMessage = "getUserData - Error: No valid LDAP-Connection.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return;
        }

        //Search
        if (($searchResult = ldap_search($this->ldapConnection, $this->baseDN, $seachParam, $retAttrFilter)) == false) 
        {
            $errorMessage = $this->getLdapError("getUserData - Error: LDAP search failed.");
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        } else {
            $message = "getUserData - LDAP search successful.";
            $this->logger->info($message);
            //$this->setStatus($message, LOGLEVEL::INFO);
        }

        //Normally only 1 entry should be found
        if (ldap_count_entries($this->ldapConnection, $searchResult) == 1) 
        {
            //$message = "getUserData - Found 1 object.";
            //$this->logger->info($message);
            //$this->setStatus($message, LOGLEVEL::INFO);
        } else if (ldap_count_entries($this->ldapConnection, $searchResult) == 0) {
            $errorMessage = "getUserData - Error: No object matching searchparameter was found.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        } else {
            $errorMessage = "getUserData - Error: Found '" . ldap_count_entries($this->ldapConnection, $searchResult) . "' objects matching searchparameter.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }

        //Get entry
        $entry = ldap_first_entry($this->ldapConnection, $searchResult);
        $attributes = ldap_get_attributes($this->ldapConnection, $entry);
//TODO: there is a bug with the ldap user class and rCountRemover func
        return $attributes;
    }

    /**
     * Gets an ldap-user of class-type LdapUser
     *
     * @param string $seachParam search paramter of form: 'uid=norman'
     *
     * @return class Return LdapUser-Class
     */
    public function getLdapUser($seachParam) 
    {
        $attrs = $this->getUserData($seachParam);
        if (isset($attrs)) 
        {
          $this->setStatus( "Wir haben dich in der Datenbank gefunden. Dein Passwort kannst du unter Mein Konto ändern/setzen.",
          LOGLEVEL::INFO);
        } else {
          $this->setStatus( "Ooops, es gab leider ein Problem mit der Datenbank. Bitte wende dich an den Servicedesk.",
          LOGLEVEL::DANGER);
          $this->logger->error("Error at LDAP-User retreval. See Logfile.");
        }
        return new LdapUser($attrs);
    }

    /**
     * Gets an ldap-user of class-type LdapUser
     *
     * @param string $seachParam search paramter of form: 'uid=norman'
     *
     * @return array Return Array of LdapUser-Classes
     */
    public function getAllLdapUser() 
    {
      $seachParam="eduPersonPrincipalName=";

      $eppnusers = $this->getAllUsers();
      $ldaparray = array();
      foreach ($eppnusers as $usereppn) {
        $userattrs = $this->getUserData($seachParam . $usereppn['edupersonprincipalname'][0]);
        $userattrs['dn'] = $usereppn['dn'];
        $ldaparray[] = new LdapUser($userattrs);
      }
      return $ldaparray;
    }

    /**
     * Gets a specific attribute from a user
     *
     * @param type $searchResult Result from getUserData
     * @param type $attributeKey The attribute name to look for
     * @return type The found attribute or false when nothing is found
     */
    public function getAttribute($searchResult, $attributeKey) 
    {
        if (!$this->connected) 
        {
            $errorMessage = "getAttribute - Error: No valid LDAP-Connection.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }

        if (in_array($attributeKey, $searchResult) AND ! is_null($searchResult[$attributeKey][0])) 
        {
            $message = "getAttribute - Found valid attribute with key '" . $attributeKey . "'.";
            $this->logger->info($message);
            //$this->setStatus($message, LOGLEVEL::INFO);
            return $searchResult[$attributeKey][0];
        } else {
            $errorMessage = "getAttribute - Error: Found no valid attribute with key '" . $attributeKey . "'.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }
    }

    /**
     * Sets the password for a user
     *
     * @param string $dname The distinguished name for the user
     * @param string $newPassword The new password
     * @return bool Return true if successfully or false when not
     *
     * @return bool True if successfully or false when not
     */
    public function setUserPassword($dname, $newPassword) 
    {
        if (!$this->connected) 
        {
            $errorMessage = "setUserPassword - Error: No valid LDAP-Connection.";
            $this->logger->error($errorMessage);
            return;
        }

        $passwordEncoded = "{CRYPT}" . crypt($newPassword, "$6$" . bin2hex(openssl_random_pseudo_bytes(16)));
        $dataToModify["userPassword"] = $passwordEncoded;

        //Deleting the object
        if ($this->modifyLDAPObject($dname, $dataToModify)) 
        {
            $message = "setUserPassword - User password for '" . $dname . "' successfully changed.";
            $this->logger->info($message);
            return true;
        } else {
            $errorMessage = $this->getLdapError("setUserPassword - Error: Failed to set user password for '" . $dname . "'.");
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }
    }

    /**
     * Close the LDAP connection
     */
    public function disconnect() 
    {
        ldap_close($this->ldapConnection);

        $errorMessage = "disconnect - Connection to LDAP closed.";
        $this->logger->info($errorMessage);
        //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
    }

    /**
     * Returns the last occured LDAP error
     *
     * @param string $message A additional message
     */
    private function getLdapError($message = null) 
    {
        if ($message === null) 
        {
            $message = '';
        }

        //Get LDAP Error Message
        $error = ldap_error($this->ldapConnection);

        if ($message !== '' && !empty($error)) 
        {
            $message .= "";
        }

        if (!empty($error)) 
        {
            $message .= $error;
        }

        //Get LDAP Error Number
        $errno = ldap_errno($this->ldapConnection);

        if (!empty($errno)) 
        {
            $message .= " (" . $errno . ")";
        }

        //Return complete error message
        return $message;
    }

    /**
     * Start the logger
     */
    private function startLogging() 
    {
        $date = date('Y-m-d');

        $ldaplogdir = getenv('SAXIDLDAPPROXY_LOG_DIR');
        
        if ($ldaplogdir === false) 
        {
            $ldaplogdir = '/var/log/www/saxid-ldap-proxy';
            //$ldaplogdir = 'C:/tmp';
        }

        $file = "{$ldaplogdir}/{$date}.log";
        $mylogger = fopen($file, 'a');

        $message = "Logger started.";
        $this->logger->info($message);
        $this->setStatus($message, LOGLEVEL::INFO);
    }

    /**
     * Stop the logger
     */
    private function stopLogging() 
    {
        $message = "Logger stopped.";
        $this->logger->info($message);
        $this->setStatus($message, LOGLEVEL::INFO);
        fclose($mylogger);
    }

    /**
     * Log an entry
     *
     */
    private function logEvent($event) {
        if (is_null($mylogger)) {
            return;
        }

        if ($this->debug) {
            $datetime = date('Y-m-d H:i:s');
            $message = "[{$datetime}] {$event}" . PHP_EOL;
            fwrite($mylogger, $message);

            //TMP Test
            print $message . "</br>";
        }
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

    public function getStatusType() {
        return $this->status['type'];
    }

    public function setLastUserUIDNumber($dname, $data) {
        if (!$this->connected) {
            $errorMessage = "setLastUserUIDNumber - Error: No valid LDAP-Connection.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }

        $dataToModify['lastUserUIDNumber'] = $data;

        $this->modifyLDAPObject($dname, $dataToModify);
    }

    public function setUserShell($dname, $shell) {
        if (!$this->connected) {
            $errorMessage = "setUserShell - Error: No valid LDAP-Connection.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }

        $dataToModify['loginShell'] = $shell;

        $this->modifyLDAPObject($dname, $dataToModify);
    }

    public function getLastUserUIDNumber($dname) {
        if (!$this->connected) {
            $errorMessage = "getLastUserUIDNumber - Error: No valid LDAP-Connection.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }

        if ($this->getLDAPObject($dname, "lastUserUIDNumber") == FALSE) {
            $errorMessage = "getLastUserUIDNumber - Error: No object found.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        } else {
            return $this->getLDAPObject($dname, "lastUserUIDNumber");
        }
    }

    public function setLastAcademyUIDNumber($dname, $data) {
        if (!$this->connected) {
            $errorMessage = "setLastAcademyUIDNumber - Error: No valid LDAP-Connection.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }

        $dataToModify['lastAcademyUIDNumber'] = $data;

        $this->modifyLDAPObject($dname, $dataToModify);
    }

    public function getLastAcademyUIDNumber($dname) {
        if (!$this->connected) {
            $errorMessage = "getLastAcademyUIDNumber - Error: No valid LDAP-Connection.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }

        if (!$this->getLDAPObject($dname, "lastAcademyUIDNumber")) {
            $errorMessage = "getLastAcademyUIDNumber - Error: No object found.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        } else {
            return $this->getLDAPObject($dname, "lastAcademyUIDNumber");
        }
    }

    public function setUIDNumberPrefix($dname, $data) {
        if (!$this->connected) {
            $errorMessage = "getAttribute - Error: No valid LDAP-Connection.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }

        $dataToModify['uidNumberPrefix'] = $data;

        $this->modifyLDAPObject($dname, $dataToModify);
    }

    public function getUIDNumberPrefix($dname) {
        if (!$this->connected) {
            $errorMessage = "getUIDNumberPrefix - Error: No valid LDAP-Connection.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }

        if (!$this->getLDAPObject($dname, "uidNumberPrefix")) {
            $errorMessage = "getUIDNumberPrefix - Error: No object found.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        } else {
            return $this->getLDAPObject($dname, "uidNumberPrefix");
        }
    }

    public function getLDAPObject($dname, $returnAttribut) {
        if (!$this->connected) {
            $errorMessage = "getLDAPObject - Error: No valid LDAP-Connection.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return;
        }

        $defaultSearchParam = "objectclass=*";

        //Search
        if (($searchResult = ldap_read($this->ldapConnection, $dname, $defaultSearchParam)) == false) {
            $errorMessage = $this->getLdapError("getLDAPObject - Error: LDAP search failed.");
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        } else {
            $message = "getLDAPObject - LDAP search successful.";
            $this->logger->info($message);
            //$this->setStatus($message, LOGLEVEL::INFO);
        }

        //Normally only 1 entry should be found
        if (ldap_count_entries($this->ldapConnection, $searchResult) == 1) {
            $message = "getLDAPObject - Found 1 object.";
            $this->logger->info($message);
            //$this->setStatus($message, LOGLEVEL::INFO);
        } else if (ldap_count_entries($this->ldapConnection, $searchResult) == 0) {
            $errorMessage = "getLDAPObject - Error: No object matching searchparameter was found.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        } else {
            $errorMessage = "getLDAPObject - Error: Found '" . ldap_count_entries($this->ldapConnection, $searchResult) . "' objects matching searchparameter.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }

        //Get entry
        $entry = ldap_first_entry($this->ldapConnection, $searchResult);
        $attributes = ldap_get_attributes($this->ldapConnection, $entry);

        if (array_key_exists($returnAttribut, $attributes)) {
            $message = "getLDAPObject - Attribute '" . $returnAttribut . "' found.";
            $this->logger->info($message);
            //$this->setStatus($message, LOGLEVEL::INFO);
            return $attributes[$returnAttribut][0];
        } else {
            $errorMessage = "getLDAPObject - Error: No attribute '" . $returnAttribut . "' found.";
            $this->logger->error($errorMessage);
            //$this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }
    }

    private function rCountRemover($arr) {
      foreach($arr as $key=>$val) {
        # (int)0 == "count", so we need to use ===
        if($key === "count") {
          unset($arr[$key]);
        }
        elseif(is_array($val)){
          $arr[$key] = $this->rCountRemover($arr[$key]);
        }
      }
      return $arr;
    }

}

abstract class LOGLEVEL {
    const ERROR = "ERROR";
    const SUCCESS = "success";
    const INFO = "info";
    const WARNING = "warning";
    const DANGER = "danger";
    const DEBUG = "DEBUG";
}
