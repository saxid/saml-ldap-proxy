<?php

namespace Saxid\SaxidLdapProxyBundle\Security\User;

/**
 * User class with all necessary infos
 */
class LdapUser
{
    protected $displayName;
    protected $cn;
    protected $sn;
    protected $uid;
    protected $uidNumber;
    protected $gidNumber;
    protected $mail;
    protected $userPassword;
    protected $o;
    protected $ou;
    protected $loginShell;
    protected $homeDirectory;
    protected $userServices;
    protected $eduPersonPrincipalName;
    protected $eduPersonEntitlement;
//    protected $organizationalUnitName;
//    protected $eduPersonAffiliation;
//    protected $eduPersonPrimaryAffiliation;
//    protected $eduPersonScopedAffiliation;
//    protected $eduPersonOrgUnitDN;

    /**
     * Initialize the user object with the values from LDAP
     * @param array $attributes Attributes from LDAP
     */
    public function __construct($attributes)
    {
      // maps the LDAP Attrs to the class attrs
      foreach ($attributes as $key => $value)
      {
          if (count($value) > 1)
          {
              $value = $value[0];
          }

          if (property_exists($this, $key))
          {
              $this->{$key} = $value;
          }
      }
    }

    /**
     * Get the value displayName
     *
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Get the value of Cn
     *
     * @return mixed
     */
    public function getCn()
    {
        return $this->cn;
    }

    /**
     * Get the value of Sn
     *
     * @return mixed
     */
    public function getSn()
    {
        return $this->sn;
    }

    /**
     * Get the value of Uid
     *
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Get the value of Uid Number
     *
     * @return mixed
     */
    public function getUidNumber()
    {
        return $this->uidNumber;
    }

    /**
     * Get the value of Gid Number
     *
     * @return mixed
     */
    public function getGidNumber()
    {
        return $this->gidNumber;
    }

    /**
     * Get the value of Mail
     *
     * @return mixed
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Get the value of User Password
     *
     * @return mixed
     */
    public function getUserPassword()
    {
        return $this->userPassword;
    }

    /**
     * Get the value of o
     *
     * @return mixed
     */
    public function getO()
    {
        return $this->o;
    }

    /**
     * Get the value of Ou
     *
     * @return mixed
     */
    public function getOu()
    {
        return $this->ou;
    }

    /**
     * Get the value of Login Shell
     *
     * @return mixed
     */
    public function getLoginShell()
    {
        return $this->loginShell;
    }

    /**
     * Get the value of Home Directory
     *
     * @return mixed
     */
    public function getHomeDirectory()
    {
        return $this->homeDirectory;
    }

    /**
     * Get the value of User Services
     *
     * @return mixed
     */
    public function getUserServices()
    {
        return $this->userServices;
    }

    /**
     * Get the value of Edu Person Principal Name
     *
     * @return mixed
     */
    public function getEduPersonPrincipalName()
    {
        return $this->eduPersonPrincipalName;
    }

    /**
     * Get the value of Edu Person Entitlement
     *
     * @return mixed
     */
    public function getEduPersonEntitlement()
    {
        return $this->eduPersonEntitlement;
    }

    /**
     * Get the value of Organizational Unit Name
     *
     * @return mixed
     */
    public function getOrganizationalUnitName()
    {
        return $this->organizationalUnitName;
    }

    /**
     * Get the value of Edu Person Affiliation
     *
     * @return mixed
     */
    public function getEduPersonAffiliation()
    {
        return $this->eduPersonAffiliation;
    }

    /**
     * Get the value of Edu Person Primary Affiliation
     *
     * @return mixed
     */
    public function getEduPersonPrimaryAffiliation()
    {
        return $this->eduPersonPrimaryAffiliation;
    }

    /**
     * Get the value of Edu Person Scoped Affiliation
     *
     * @return mixed
     */
    public function getEduPersonScopedAffiliation()
    {
        return $this->eduPersonScopedAffiliation;
    }

    /**
     * Get the value of Edu Person Org Unit
     *
     * @return mixed
     */
    public function getEduPersonOrgUnitDN()
    {
        return $this->eduPersonOrgUnitDN;
    }


    /**
     * Set the value of User Password
     *
     * @param mixed userPassword
     *
     * @return self
     */
    public function setUserPassword($userPassword)
    {
        $this->userPassword = $userPassword;

        return $this;
    }

}
