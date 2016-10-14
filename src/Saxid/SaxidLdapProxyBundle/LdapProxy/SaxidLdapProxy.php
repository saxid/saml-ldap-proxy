<?php

namespace Saxid\SaxidLdapProxyBundle\LdapProxy;

/**
 * Fetches data from an SAML2 IdP and passes them through to an LDAP server
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
    private $debug = true;
    private $logger;
    private $status;

    /**
     * Creates LDAP access object
     * @param string $ldap_host The ldap host address
     * @param string $ldap_port The ldap port
     * @param string $ldap_user The ldap user to bind
     * @param string $ldap_pass The ldap user password
     * @param string $baseDN The baseDN for search
     */
    function __construct($ldap_host, $ldap_port, $ldap_user, $ldap_pass, $baseDN)
    {
        $this->ldapHost = $ldap_host;
        $this->ldapPort = $ldap_port;
        $this->ldapUser = $ldap_user;
        $this->ldapPassword = $ldap_pass;
        $this->baseDN = $baseDN;

        //Start logger
        $this->startLogging();
    }

    /**
     * Connect to LDAP and tries anonymous bind to check if connection successfully
     * 
     */
    public function connect()
    {
        //Connection to LDAP
        //ldap_connect always has return value, even if LDAP not reachable
        $this->ldapConnection = ldap_connect($this->ldapHost);

        //Set LDAP version
        ldap_set_option($this->ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);

        //Check trough anonymous bind if LDAP is reachable
        if (@ldap_bind($this->ldapConnection))
        {
            $message = "connect - Connection to LDAP-Server (" . $this->ldapHost . ") successfully established.";
            $this->logEvent($message);
            $this->setStatus($message, LOGLEVEL::INFO);
        }
        else
        {
            $errorMessage = $this->getLdapError("connect - Error: Connection to LDAP-Server (" . $this->ldapHost . ") could not be established.");
            $this->logEvent($errorMessage);
            $this->setStatus($errorMessage, LOGLEVEL::ERROR);
            $this->connected = false;
            return false;
        }

        //Authentication bind to access data (anonymous cant read)
        if (@ldap_bind($this->ldapConnection, $this->ldapUser, $this->ldapPassword))
        {
            $message = "connect - Bind successfull.";
            $this->logEvent($message);
            $this->setStatus($message, LOGLEVEL::INFO);
            $this->connected = true;
        }
        else
        {
            $errorMessage = $this->getLdapError("connect - Error: Bind failed.");
            $this->logEvent($errorMessage);
            $this->setStatus($errorMessage, LOGLEVEL::ERROR);
            $this->connected = false;
            return false;
        }
        return true;
    }

    /**
     * Check if user exists in LDAP
     * 
     * @param string $seachParam Search parameter in following form: 'uid=norman'
     * @return bool Return object if user was found or false when not
     */
    public function existsUser($seachParam)
    {
        if (!$this->connected)
        {
            $errorMessage = "existsUser - Error: No valid LDAP-Connection.";
            $this->logEvent($errorMessage);
            $this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return;
        }

        //returnFilter for datareduction
        $returnFilter = array("dn");

        return $this->getUserData($seachParam, $returnFilter);
    }

    /**
     * Add a user to LDAP
     * 
     * @param string $dn The distinguished name for the new user
     * @param array $data Data array with userdata
     * @return bool True if successfully or false when not
     */
    public function addUser($dn, $data)
    {
        if (!$this->connected)
        {
            $errorMessage = "addUser - Error: No valid LDAP-Connection.";
            $this->logEvent($errorMessage);
            $this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return;
        }

        //Adding the object
        if (ldap_add($this->ldapConnection, $dn, $data))
        {
            $message = "addUser - User '" . $dn . "' successfully added.";
            $this->logEvent($message);
            $this->setStatus($message, LOGLEVEL::INFO);
            return true;
        }
        else
        {
            $errorMessage = $this->getLdapError("addUser - Error: Failed to add user.");
            $this->logEvent($errorMessage);
            $this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }
    }

    /**
     * Modify a user in LDAP
     * 
     * @param string $dn The distinguished name for the user
     * @param array $data Data array with userdata
     * @return bool True if successfully or false when not
     */
    public function modifyUser($dn, $data)
    {
        if (!$this->connected)
        {
            $errorMessage = "modifyUser - Error: No valid LDAP-Connection.";
            $this->logEvent($errorMessage);
            $this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return;
        }

        //Modify the object
        if (ldap_modify($this->ldapConnection, $dn, $data))
        {
            $message = "modifyUser - User '" . $dn . "' successfully updated.";
            $this->logEvent($message);
            $this->setStatus($message, LOGLEVEL::INFO);
            return true;
        }
        else
        {
            $errorMessage = $this->getLdapError("modifyUser - Error: Update of user failed.");
            $this->logEvent($errorMessage);
            $this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }
    }

    /**
     * Delete a user in LDAP
     * 
     * @param string $dn The distinguished name for the user
     * @return bool True if successfully or false when not
     */
    public function deleteUser($dn)
    {
        if (!$this->connected)
        {
            $errorMessage = "deleteUser - Error: No valid LDAP-Connection.";
            $this->logEvent($errorMessage);
            $this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return;
        }

        //Deleting the object
        if (ldap_delete($this->ldapConnection, $dn))
        {
            $message = "deleteUser - User '" . $dn . "' successfully deleted.";
            $this->logEvent($message);
            $this->setStatus($message, LOGLEVEL::INFO);
            return true;
        }
        else
        {
            $errorMessage = $this->getLdapError("deleteUser - Error: Delete of '" . $dn . "' failed.");
            $this->logEvent($errorMessage);
            $this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }
    }

    /**
     * Get userdata from a user
     * 
     * @param string $seachParam search paramter in following form: 'uid=norman'
     * @param array $returnAttributesFilter Filters the return values, default everything is shown. Example: array("dn, sn, mail, ...")
     *
     * @return array Return array with userdata from user
     */
    public function getUserData($seachParam, $returnAttributesFilter = array("*"))
    {
        if (!$this->connected)
        {
            $errorMessage = "getUserData - Error: No valid LDAP-Connection.";
            $this->logEvent($errorMessage);
            $this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return;
        }

        //Search
        if (($searchResult = ldap_search($this->ldapConnection, $this->baseDN, $seachParam, $returnAttributesFilter)) == false)
        {
            $errorMessage = $this->getLdapError("getUserData - Error: LDAP search failed.");
            $this->logEvent($errorMessage);
            $this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }
        else
        {
            $message = "getUserData - LDAP search successful.";
            $this->logEvent($message);
            $this->setStatus($message, LOGLEVEL::INFO);
        }

        //Normally only 1 entry should be found
        if (ldap_count_entries($this->ldapConnection, $searchResult) == 1)
        {
            $message = "getUserData - Found 1 object.";
            $this->logEvent($message);
            $this->setStatus($message, LOGLEVEL::INFO);
        }
        else if (ldap_count_entries($this->ldapConnection, $searchResult) == 0)
        {
            $errorMessage = "getUserData - Error: No object matching searchparameter was found.";
            $this->logEvent($errorMessage);
            $this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }
        else
        {
            $errorMessage = "getUserData - Error: Found '" . ldap_count_entries($this->ldapConnection, $searchResult) . "' objects matching searchparameter.";
            $this->logEvent($errorMessage);
            $this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }

        //Get entry
        $entry = ldap_first_entry($this->ldapConnection, $searchResult);
        $attributes = ldap_get_attributes($this->ldapConnection, $entry);

        return $attributes;
    }

    /**
     * Get specific attribute from a user
     * @param type $searchResult Result from getUserData
     * @param type $attributeKey The attribute name to look for
     * @return type The found attribute or false when nothing is found
     */
    public function getAttribute($searchResult, $attributeKey)
    {
        if (!$this->connected)
        {
            $errorMessage = "getAttribute - Error: No valid LDAP-Connection.";
            $this->logEvent($errorMessage);
            $this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }

        if (in_array($attributeKey, $searchResult) AND ! is_null($searchResult[$attributeKey][0]))
        {
            $message = "getAttribute - Found valid attribute with key '" . $attributeKey . "'.";
            $this->logEvent($message);
            $this->setStatus($message, LOGLEVEL::INFO);
            return $searchResult[$attributeKey][0];
        }
        else
        {
            $errorMessage = "getAttribute - Error: Found no valid attribute with key '" . $attributeKey . "'.";
            $this->logEvent($errorMessage);
            $this->setStatus($errorMessage, LOGLEVEL::ERROR);
            return false;
        }
    }

    /**
     * Set the password from a user
     * 
     * @param string $dn The distinguished name for the user
     * @param string $newPassword The new password
     * @return bool Return true if successfully or false when not
     *
     * @return bool True if successfully or false when not
     */
    public function setUserPassword($dn, $newPassword)
    {
        if (!$this->connected)
        {
            $errorMessage = "setUserPassword - Error: No valid LDAP-Connection.";
            $this->logEvent($errorMessage);
            return;
        }

        //TODO, ggf. Logik um Passwort zu verifizieren
        $dataToModify["userPassword"] = $newPassword;

        //Deleting the object
        if ($this->modifyUser($dn, $dataToModify))
        {
            $message = "setUserPassword - User password for '" . $dn . "' successfully changed.";
            $this->logEvent($message);
            return true;
        }
        else
        {
            $errorMessage = $this->getLdapError("setUserPassword - Error: Failed to set user password for '" . $dn . "'.");
            $this->logEvent($errorMessage);
            $this->setStatus($errorMessage, LOGLEVEL::ERROR);
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
        $this->logEvent($errorMessage);
        $this->setStatus($errorMessage, LOGLEVEL::ERROR);
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
    public function startLogging()
    {
        $date = date('Y-m-d');

        $ldaplogdir = getenv('SAXIDLDAPPROXY_LOG_DIR');
        if ($ldaplogdir === false)
        {
            $ldaplogdir = '/var/log/www/saxid-ldap-proxy';
            //$ldaplogdir = 'C:/tmp';
            //C:\TMP
        }

        $file = "{$ldaplogdir}/{$date}.log";
        $this->logger = fopen($file, 'a');

        $message = "Logger started.";
        $this->logEvent($message);
        $this->setStatus($message, LOGLEVEL::INFO);
    }

    /**
     * Stop the logger
     */
    public function stopLogging()
    {
        $message = "Logger stopped.";
        $this->logEvent($message);
        $this->setStatus($message, LOGLEVEL::INFO);
        fclose($this->logger);
    }

    /**
     * Log an entry
     * 
     */
    private function logEvent($event)
    {
        if (is_null($this->logger))
        {
            return;
        }

        if ($this->debug)
        {
            $datetime = date('Y-m-d H:i:s');
            $message = "[{$datetime}] {$event}" . PHP_EOL;
            fwrite($this->logger, $message);

            //TMP Test
            print $message . "</br>";
        }
    }

    private function setStatus($message, $type = 'success')
    {
        $this->status = array('message' => $message, 'type' => $type);
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getStatusMessage()
    {
        return $this->status['message'];
    }

    public function getStatusType()
    {
        return $this->status['type'];
    }
}
abstract class LOGLEVEL
{
    const ERROR = "ERROR";
    const INFO = "INFO";
    const WARNING = "WARNING";
    const DEBUG = "DEBUG";

}