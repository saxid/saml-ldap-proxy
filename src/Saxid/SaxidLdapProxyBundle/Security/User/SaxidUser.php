<?php

namespace Saxid\SaxidLdapProxyBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
//use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

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

    protected $oldPassword;
    // to meet the IDM and IT security password strength
    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 9,
     *      max = 128,
     *      minMessage = "Your password must be at least {{ limit }} characters long",
     *      maxMessage = "Your password cannot be longer than {{ limit }} characters"
     * )
     * @Assert\Regex(
     *     pattern="/^\S*(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[0-9])(?=\S*[!@#$%-])\S*$/",
     *     match=true,
     *     message="Your password must contain a number, one special char from the list '!@#$%-', upper and lowercase characters."
     * )
     */
    private $password;
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
        'uni-leipzig.de' => 'Universität Leipzig',
        'htw-dresden.de' => 'Hochschule für Technik und Wirtschaft Dresden',
        'hs-mittweida.de' => 'Hochschule Mittweida (FH)',
        'htwk-leipzig.de' => 'Hochschule für Technik, Wirtschaft und Kultur Leipzig',
            // 'rnd.feide.no'  => 'Feide NO',
    );
    private static $attributeMapping = array(// SAML 2
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
// SAML 1
        'urn:mace:dir:attribute-def:sn' => 'surname',
        'urn:mace:dir:attribute-def:givenName' => 'givenName',
        'urn:mace:dir:attribute-def:cn' => 'commonName',
        'urn:mace:dir:attribute-def:mail' => 'email',
        'urn:mace:dir:attribute-def:eduPersonPrincipalName' => 'eduPersonPrincipalName',
        'urn:mace:dir:attribute-def:eduPersonAffiliation' => 'eduPersonAffiliation',
        'urn:mace:dir:attribute-def:eduPersonScopedAffiliation' => 'eduPersonScopedAffiliation',
        'urn:mace:dir:attribute-def:eduPersonEntitlement' => 'eduPersonEntitlement',
        //'urn:oid:1.3.6.1.1.1.1.3' => 'homeDirectory',
        //'urn:oid:1.3.6.1.1.1.1.4' => 'loginShell',
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
        // Map SAML2 & 1 attributes into class properties
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
        $this->roles = array("SAXIDUSER");
        $this->setAcademyDomain();
        $this->setAcademy();
        $this->setDisplayName($this->displayName);
        $this->setUid(strstr($this->getEduPersonPrincipalName(), '@', true));
        $this->username = $this->getEduPersonPrincipalName();

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
        if (isset($this->username))
        {
            return $this->username;
        }
        else
        {
            return "DefaultUsername";
        }
    }

    public function getSalt()
    {
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

    // example: uid=tud_def
    public function setUid($uid)
    {
        $this->uid = self::$uidPrefixMapping[$this->getAcademyDomain()] . "_" . $uid;
        return $this;
    }

    public function getUid()
    {
        if (isset($this->uid))
        {
            return $this->uid;
        }
        else
        {
            return "DefaultUid";
        }
    }

    // uncrypted PW, pass is crypted and stored in SLP-Class/Service
    public function setPassword($password)
    {
        #$this->password = '{CRYPT}' . password_hash($password, PASSWORD_BCRYPT);
        #$salt = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', 4)), 0, 4);
        #$this->password = '{SSHA}' . base64_encode(sha1( $password.$salt, TRUE ) . $salt);
        #$this->password = '{MD5}' . base64_encode(md5($password, TRUE));
        #$this->password = "{CRYPT}" . crypt($password, "$6$" . bin2hex(openssl_random_pseudo_bytes(16))); SHA-512
        $this->password = $password;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setEduPersonPrincipalName($eduPersonPrincipalName)
    {
        $this->eduPersonPrincipalName = $eduPersonPrincipalName;
        return $this;
    }

    public function getEduPersonPrincipalName()
    {

        if (isset($this->eduPersonPrincipalName))
        {
            return $this->eduPersonPrincipalName;
        }
        else
        {
            return "DefaultEduPersonPrincipalName";
        }
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
        else
        {
            return "DefaultEduPersonAffiliation";
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

        if (isset($this->eduPersonPrimaryAffiliation))
        {
            return $this->eduPersonPrimaryAffiliation;
        }
        else
        {
            return "DefaultEduPersonPrimaryAffiliation";
        }
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
        else
        {
            return "DefaultEduPersonScopedAffiliation";
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
        if (isset($this->surname))
        {
            return $this->surname;
        }
        else
        {
            return "DefaultSurname";
        }
    }

    public function setGivenName($givenName)
    {
        $this->givenName = $givenName;
        return $this;
    }

    public function getGivenName()
    {

        if (isset($this->givenName))
        {
            return $this->givenName;
        }
        else
        {
            return "DefaultGivenName";
        }
    }

    public function setUidNumber($uidNumber)
    {
        $this->uidNumber = $uidNumber;
        return $this;
    }

    public function getUidNumber()
    {
        if (isset($this->uidNumber))
        {
            return $this->uidNumber;
        }
        else
        {
            return "DefaultUidNumber";
        }
    }

    public function setEduPersonEntitlement($eduPersonEntitlement)
    {
        $this->eduPersonEntitlement = $eduPersonEntitlement;
        return $this;
    }

    public function getEduPersonEntitlement()
    {
        if (isset($this->eduPersonEntitlement))
        {
            return $this->eduPersonEntitlement;
        }
        else
        {
            return "DefaultEduPersonEntitlement";
        }
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
            if (is_array($this->organizationalUnitName) and ! $asArray)
            {
                $return = implode(', ', $this->organizationalUnitName);
            }
            else
            {
                $return = $this->organizationalUnitName;
            }
        }
        else
        {
            return "DefaultOrganizationalUnitName";
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
        if (isset($this->commonName))
        {
            return $this->commonName;
        }
        else
        {
            return "DefaultCommonName";
        }
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail()
    {

        if (isset($this->email))
        {
            return $this->email;
        }
        else
        {
            return "DefaultEmail";
        }
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
        else
        {
            return $this->getParameter('ldap_baseDN');
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

        if (isset($this->displayName))
        {
            return $this->displayName;
        }
        else
        {
            return "DefaultDisplayName";
        }
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
        if (isset($this->academy))
        {
            return $this->academy;
        }
        else
        {
            return "DefaultAcademy";
        }
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
        if (isset($this->academyDomain))
        {
            return $this->academyDomain;
        }
        else
        {
            return "DefaultAcademyDomain";
        }
    }

    public function isFromSaxonAcademy()
    {
        $domain = $this->getAcademyDomain();
        return array_key_exists($domain, self::$academies);
    }

    /**
     * TODO: Adapt this Function to fit your local IDM needs
     * check for local IDM uidnumber collision.
     * Unix UidNumber max length 2.147.483.648
     */
    public function generateSaxIDUIDNumber($uidNumberPrefix, $uidNumber)
    {
        if ($this->isFromSaxonAcademy())
        {
            // Determine required 0s to fill up the UIDNumber
            $numberNullUIDNumber = 6 - strlen($uidNumber);

            // Fill with 0s
            $nullsUIDNumber = '';
            for ($i = 1; $i <= $numberNullUIDNumber; $i++)
            {
                $nullsUIDNumber .= '0';
            }

            // Determine required 0s to fill up the UIDPrefix
            $numberNullUIDPrefix = 3 - strlen($uidNumberPrefix);

            // Fill with 0s
            $nullsUIDPrefix = '';
            for ($i = 1; $i <= $numberNullUIDPrefix; $i++)
            {
                $nullsUIDPrefix .= '0';
            }

            // Append UID, UIDNumbers start with 1 to avoid conflicts with local IDM
            $saxidUid = '1' . $nullsUIDPrefix . $uidNumberPrefix . $nullsUIDNumber . $uidNumber;
        }
        else
        {
            $saxidUid = false;
        }

        return $saxidUid;
    }

    public function generateRandomPassword($length = 8)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*()_-=+?";
        $password = substr(str_shuffle($chars), 0, $length);
        return $password;
    }

    public function createLdapDataArray($isAdd = false)
    {
        // objectClasses
        $data['objectclass'][] = 'top';
        $data['objectclass'][] = 'inetOrgPerson';
        $data['objectclass'][] = 'posixAccount';
        $data['objectclass'][] = 'shadowAccount';
        $data['objectclass'][] = 'eduPerson';
        $data['objectclass'][] = 'saxID';

        // Set basic user information
        $data['givenName'] = $this->getGivenName();
        $data['sn'] = $this->getSurname();
        $data['cn'] = $this->getCommonName();
        $data['mail'] = $this->getEmail();
        //$data['ou'] = $this->getOrganizationalUnitName();
        $data['o'] = $this->getAcademy();
        $data['displayName'] = $this->getDisplayName();
        //$data['userPassword'] = $this->getPassword();

        // eduPerson
        $data['eduPersonEntitlement'] = $this->getEduPersonEntitlement();
        $data['eduPersonPrincipalName'] = $this->getEduPersonPrincipalName();
        //$data['eduPersonAffiliation'] = $this->getEduPersonAffiliation(true);
        //$data['eduPersonOrgUnitDN'] = $this->getEduPersonOrgUnitDN(true);
        //$data['eduPersonPrimaryAffiliation'] = $this->getEduPersonPrimaryAffiliation();
        //$data['eduPersonScopedAffiliation'] = $this->getEduPersonScopedAffiliation(true);

        // nis/posixAccount
        $data['uid'] = $this->getUid();
        // Only create UidNumber for new user
        if ($isAdd)
        {
            $data['uidNumber'] = $this->getUidNumber();
        }
        //$data['gecos'] = $this->getGecos();
        $data['gidNumber'] = 1000;
        $data['homeDirectory'] = "/home/" . $this->getUid();
        $data['loginShell'] = '/bin/bash';

        //saxID
        $data['userServices'] = "hpc";

        return $data;
    }

    public function createLdapUserDN($baseDN)
    {
        return "cn=" . $this->getCommonName() . ",o=" . $this->getAcademyDomain() . "," . $baseDN;
    }

    public function createLdapOrganizationDN($baseDN)
    {
        return "o=" . $this->getAcademyDomain() . "," . $baseDN;
    }

    public function mydump()
    {
        print_r(get_object_vars($this));
    }

    public function __toString()
    {
        return "{$this->getDisplayName()} ({$this->getAcademy()})";
    }
}
