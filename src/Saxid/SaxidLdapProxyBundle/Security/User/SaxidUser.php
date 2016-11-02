<?php

namespace Saxid\SaxidLdapProxyBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * User class with all necessary infos
 */
class SaxidUser implements UserInterface, EquatableInterface
{
    //username, roles, salt are interface attributes
    private $surname;
    private $givenName;
    private $commonName;
    private $displayName;
    private $uid;
    private $uidNumber;
    private $password;
    private $uncryptPassword;
    private $username;
    private $salt;
    private $email;
    private $academy;
    private $academyDomain;
    private $roles;
    private $organizationalUnitName;
    private $eduPersonPrincipalName;
    private $eduPersonAffiliation;
    private $eduPersonPrimaryAffiliation;
    private $eduPersonScopedAffiliation;
    private $eduPersonOrgUnitDN;
    private $eduPersonEntitlement;
    private static $academies = array(
        'tu-dresden.de' => 'Technische Universität Dresden',
        'tu-chemnitz.de' => 'Technische Universität Chemnitz',
        'tu-freiberg.de' => 'Technische Universität Freiberg',
            // 'rnd.feide.no'  => 'Feide NO',
    );
    private static $attributeMapping = array(
        'urn:oid:0.9.2342.19200300.100.1.1' => 'uid',
        'urn:oid:2.5.4.4' => 'surname',
        'urn:oid:2.5.4.42' => 'givenName',
        'urn:oid:2.5.4.3' => 'commonName',
        'urn:oid:0.9.2342.19200300.100.1.3' => 'email',
        'urn:oid:2.16.840.1.113730.3.1.241' => 'displayName',
        'urn:oid:2.5.4.11' => 'organizationalUnitName',
        'urn:oid:1.3.6.1.4.1.5923.1.1.1.6' => 'eduPersonPrincipalName',
        'urn:oid:1.3.6.1.4.1.5923.1.1.1.1' => 'eduPersonAffiliation',
        'urn:oid:1.3.6.1.4.1.5923.1.1.1.5' => 'eduPersonPrimaryAffiliation',
        'urn:oid:1.3.6.1.4.1.5923.1.1.1.9' => 'eduPersonScopedAffiliation',
        'urn:oid:1.3.6.1.4.1.5923.1.1.1.4' => 'eduPersonOrgUnitDN',
        'urn:oid:1.3.6.1.4.1.5923.1.1.1.7' => 'eduPersonEntitlement',
            //'urn:oid:1.3.6.1.1.1.1.3' => 'homeDirectory',
            //'urn:oid:2.5.6.0' => 'top',
            //'urn:oid:1.3.6.1.1.1.1.4' => 'loginShell',
            //'urn:oid:1.3.6.1.1.1.2.1' => 'shadowAccount',
    );
    private static $uidPrefixMapping = array(
        'tu-dresden.de' => 'tud',
        'uni-leipzig.de' => 'ul',
        'tu-chemnitz.de' => 'tuc',
        'htw-dresden.de' => 'htwdd',
        'tu-freiberg.de' => 'tubaf',
        'hszg.de' => 'hszg',
        'htwk-leipzig.de' => 'htwkl',
        'fh-zwickau.de' => 'hsz',
        'hs-mittweida.de' => 'hsmw',
        'hfbk-dresden.de' => 'hfbk',
        'hfmdd.de' => 'hfmdd',
        'hgb-leipzig.de' => 'hgbl',
        'hmt-leipzig.de' => 'hmtl',
            // 'rnd.feide.no'    => '40',
    );

    /**
     * Initialize the user object with the values from the IdP
     * @param array $attributes Attributes from IdP
     */
    public function __construct($attributes)
    {
        // Map SAML2 attributes into class properties
        foreach ($attributes as $key => $attribute)
        {
            if (in_array($key, array_keys(self::$attributeMapping)))
            {
                $key = self::$attributeMapping[$key];
            }

            if (count($attribute) == 1)
            {
                $attribute = $attribute[0];
            }

            if (property_exists($this, $key))
            {
                $this->{$key} = $attribute;
            }
        }

        //Generate missing attributes from IdP
        $this->roles = array("User");
        $this->setAcademyDomain();
        $this->setAcademy();
        $this->setDisplayName($this->displayName);
        $this->uid = substr(strstr($this->getEduPersonPrincipalName(), '@'), 0);
        $this->username = $this->getEppn();

        //########## PASSWORD #######
        //TMP, festes Passwort
        //$this->setPassword('knack');
        //$this->setUncryptPassword('knack');

        //###########################
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getSalt() {
        return $this->salt;
    }

    public function eraseCredentials()
    {
        // do nothing
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof SaxidUser)
        {
            return false;
        }

        if ($this !== $user)
        {
            return false;
        }

        if ($this->password !== $user->getPassword())
        {
            return false;
        }

        if ($this->username !== $user->getUsername())
        {
            return false;
        }

        return true;
    }

    public function setUid($uid)
    {
        $this->uid = self::$uidPrefixMapping[$this->getAcademyDomain()] . "_" . $uid;
        return $this;
    }

    public function getUid()
    {
        return $this->uid;
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

    public function getPassword()
    {
        return $this->password;
    }

    public function setUncryptPassword($password)
    {
        $this->uncryptPassword = $password;
        return $this;
    }

    public function getUncryptPassword()
    {
        return $this->uncryptPassword;
    }

    public function setEduPersonPrincipalName($eduPersonPrincipalName)
    {
        $this->eduPersonPrincipalName = $eduPersonPrincipalName;
        return $this;
    }

    public function getEduPersonPrincipalName()
    {
        return $this->eduPersonPrincipalName;
    }

    //Alias for getEduPersonPrincipalName();
    public function getEppn()
    {
        return $this->getEduPersonPrincipalName();
    }

    public function setEduPersonAffiliation($eduPersonAffiliation)
    {
        $this->eduPersonAffiliation = $eduPersonAffiliation;
        return $this;
    }

    public function getEduPersonAffiliation($asArray = false)
    {
        $return = null;
        if (!empty($this->eduPersonAffiliation))
        {
            $return = $asArray ? $this->eduPersonAffiliation : implode(', ', $this->eduPersonAffiliation);
        }
        return $return;
    }

    public function setEduPersonPrimaryAffiliation($eduPersonPrimaryAffiliation)
    {
        $this->eduPersonPrimaryAffiliation = $eduPersonPrimaryAffiliation;
        return $this;
    }

    public function getEduPersonPrimaryAffiliation()
    {
        return $this->eduPersonPrimaryAffiliation;
    }

    public function setEduPersonScopedAffiliation($eduPersonScopedAffiliation)
    {
        $this->eduPersonScopedAffiliation = $eduPersonScopedAffiliation;
        return $this;
    }

    public function getEduPersonScopedAffiliation($asArray = false)
    {
        $return = null;
        if (!empty($this->eduPersonScopedAffiliation))
        {
            $return = $asArray ? $this->eduPersonScopedAffiliation : implode(', ', $this->eduPersonScopedAffiliation);
        }
        return $return;
    }

    public function setSurname($surname)
    {
        $this->surname = $surname;
        return $this;
    }

    public function getSurname()
    {
        return $this->surname;
    }

    public function setGivenName($givenName)
    {
        $this->givenName = $givenName;
        return $this;
    }

    public function getGivenName()
    {
        return $this->givenName;
    }

    public function setUidNumber($uidNumber)
    {
        $this->uidNumber = $uidNumber;
        return $this;
    }

    public function getUidNumber()
    {
        return $this->uidNumber;
    }

    public function setEduPersonEntitlement($eduPersonEntitlement)
    {
        $this->eduPersonEntitlement = $eduPersonEntitlement;
        return $this;
    }

    public function getEduPersonEntitlement()
    {
        return $this->eduPersonEntitlement;
    }

    public function setOrganizationalUnitName($organizationalUnitName)
    {
        $this->organizationalUnitName = $organizationalUnitName;
        return $this;
    }

    public function getOrganizationalUnitName($asArray = false)
    {
        $return = null;

        if (!empty($this->organizationalUnitName))
        {
            if (is_array($this->organizationalUnitName) and !$asArray)
            {
                $return = implode(', ', $this->organizationalUnitName);
            }
            else
            {
                $return = $this->organizationalUnitName;
            }
        }
        return $return;
    }

    public function setCommonName($commonName)
    {
        $this->commonName = $commonName;
        return $this;
    }

    public function getCommonName()
    {
        return $this->commonName;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEduPersonOrgUnitDN($eduPersonOrgUnitDN)
    {
        $this->eduPersonOrgUnitDN = $eduPersonOrgUnitDN;
        return $this;
    }

    public function getEduPersonOrgUnitDN($asArray = false)
    {
        $return = null;

        if (!empty($this->eduPersonOrgUnitDN))
        {
            if (is_array($this->eduPersonOrgUnitDN) and ! $asArray)
            {
                $return = implode(', ', $this->eduPersonOrgUnitDN);
            }
            else
            {
                $return = $this->eduPersonOrgUnitDN;
            }
        }
        return $return;
    }

    public function setDisplayName($displayName)
    {
        if (!isset($displayName))
        {
            $displayName = $this->givenName . " " . $this->surname;
            $this->setDisplayName($displayName);
        }

        $this->displayName = $displayName;
        return $this;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function getGecos()
    {
        return $this->getDisplayName();
    }

    public function setAcademy()
    {
        $domain = $this->getAcademyDomain();

        if (array_key_exists($domain, self::$academies))
        {
            $return = self::$academies[$domain];
        }
        else
        {
            $return = $domain;
        }

        $this->academy = $return;
        return $this;
    }

    public function getAcademy()
    {
        return $this->academy;
    }

    /**
    * Sets the Academy Domain for a user using his or her ePPN
    *
    */
    public function setAcademyDomain()
    {
        $this->academyDomain = substr(strstr($this->getEduPersonPrincipalName(), '@'), 1);
        return $this;
    }

    public function getAcademyDomain()
    {
        return $this->academyDomain;
    }

    public function isFromSaxonAcademy()
    {
        $domain = $this->getAcademyDomain();
        return array_key_exists($domain, self::$academies);
    }

    public function generateSaxIDUIDNumber()
    {
        if ($this->isFromSaxonAcademy())
        {
            // Length of random number
            $digits = 8;

            // Generate random number
            $randomID = str_pad(rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);

            // Set academy prefix; start with 30 for compatibility resons with local IDM
            // former setup: used 2 num digits starting with 10 for each uni
            $prefix = 30;

            // Determine required 0s to fill up
            $numberNulls = 10 - strlen($prefix) - strlen($randomID);

            // Fill with 0s
            $nulls = '';
            for ($i = 1; $i <= $numberNulls; $i++)
            {
                $nulls .= '0';
            }

            // Append UID
            $saxidUid = $prefix . $nulls . $randomID;
        }
        else
        {
            $saxidUid = false;
        }

        return $saxidUid;
    }

    public function createLdapDataArray($isAdd = false)
    {
        // Set basic user information
        $data['givenName'] = $this->getGivenName();
        $data['sn'] = $this->getSurname();
        $data['cn'] = $this->getCommonName();
        $data['mail'] = $this->getEmail();
        $data['ou'] = $this->getOrganizationalUnitName();
        $data['o'] = $this->getAcademy();
        $data['displayName'] = $this->getDisplayName();
        //$data['userPassword'] = $this->getPassword();

        // eduPerson
        $data['eduPersonAffiliation'] = $this->getEduPersonAffiliation(true);
        $data['eduPersonEntitlement'] = $this->getEduPersonEntitlement();
        $data['eduPersonOrgUnitDN'] = $this->getEduPersonOrgUnitDN(true);
        $data['eduPersonPrimaryAffiliation'] = $this->getEduPersonPrimaryAffiliation();
        $data['eduPersonPrincipalName'] = $this->getEppn();
        $data['eduPersonScopedAffiliation'] = $this->getEduPersonScopedAffiliation(true);

        // nis/posixAccount
        $data['uid'] = $this->getUid();
        // Only create UidNumber for new user
        if ($isAdd)
        {
            $data['uidNumber'] = $this->getUidNumber();
        }
        $data['gecos'] = $this->getGecos();
        $data['gidNumber'] = 0;
        $data['homeDirectory'] = "/home/" . $this->getUid();
        $data['loginShell'] = '/bin/bash';

        // objectClasses
        $data['objectclass'][] = 'top';
        $data['objectclass'][] = 'inetOrgPerson';
        $data['objectclass'][] = 'posixAccount';
        $data['objectclass'][] = 'shadowAccount';
        $data['objectclass'][] = 'eduPerson';

        return $data;
    }

    public function createLdapUserDN()
    {
        return "cn=" . $this->getCommonName() . ",o=" . $this->getAcademyDomain() . ",dc=sax-id,dc=de";
    }
    
    public function createLdapOrganizationDN()
    {
        return "o=" . $this->getAcademyDomain() . ",dc=sax-id,dc=de";
    }

    public function dump()
    {
        print_r(get_object_vars($this));
    }

    public function __toString()
    {
        return "{$this->getDisplayName()} ({$this->getAcademy()})";
    }
}
