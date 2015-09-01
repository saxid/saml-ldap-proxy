<?php

namespace Saxid\SaxidLdapProxyBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

class SaxidUser implements UserInterface, EquatableInterface
{
    private $username;
    private $salt;
    private $roles;

    private $uid;
    private $eduPersonPrincipalName;
    private $eduPersonAffiliation;
    private $eduPersonPrimaryAffiliation;
    private $eduPersonScopedAffiliation;
    private $surname;
    private $givenName;
    private $uidNumber;
    private $password;
    private $eduPersonEntitlement;
    private $organizationalUnitName;
    private $commonName;
    private $email;
    private $eduPersonOrgUnitDN;
    private $displayName;
    private $academy;
    private $academyDomain;
    
    private static $academies = array(
        'tu-dresden.de' => 'Technische Universität Dresden',
    );

    private static $attributeMapping = array(
        'urn:oid:0.9.2342.19200300.100.1.1' => 'uid',
        'urn:oid:1.3.6.1.4.1.5923.1.1.1.6'  => 'eduPersonPrincipalName',
        'urn:oid:1.3.6.1.4.1.5923.1.1.1.1'  => 'eduPersonAffiliation',
        'urn:oid:1.3.6.1.4.1.5923.1.1.1.5'  => 'eduPersonPrimaryAffiliation',
        'urn:oid:1.3.6.1.4.1.5923.1.1.1.9'  => 'eduPersonScopedAffiliation',
        'urn:oid:2.5.4.4'                   => 'surname',
        'urn:oid:2.5.4.42'                  => 'givenName',
        'urn:oid:1.3.6.1.4.1.5923.1.1.1.7'  => 'eduPersonEntitlement',
        'urn:oid:2.5.4.11'                  => 'organizationalUnitName',
        'urn:oid:2.5.4.3'                   => 'commonName',
        'urn:oid:0.9.2342.19200300.100.1.3' => 'email',
        'urn:oid:1.3.6.1.4.1.5923.1.1.1.4'  => 'eduPersonOrgUnitDN',
        'urn:oid:2.16.840.1.113730.3.1.241' => 'displayName',
    );

    private static $uidPrefixMapping = array(
        'tu-dresden.de'   => '10',
        'uni-leipzig.de'  => '11',
        'tu-chemnitz.de'  => '12',
        'htw-dresden.de'  => '13',
        'tu-freiberg.de'  => '14',
        'hszg.de'         => '15',
        'htwk-leipzig.de' => '16',
        'fh-zwickau.de'   => '17',
        'hs-mittweida.de' => '18',
        'hfbk-dresden.de' => '19',
        'hfmdd.de'        => '20',
        'hgb-leipzig.de'  => '21',
        'hmt-leipzig.de'  => '22',
    );

    public function __construct($attributes)
    {
        // Map SAML2 attributes into class properties
        foreach($attributes as $key => $attribute) {
            if(in_array($key, array_keys(self::$attributeMapping))) {
                $key = self::$attributeMapping[$key];
            }
            
            if(count($attribute) == 1) {
                $attribute = $attribute[0];
            }

            if(property_exists($this, $key)) {
                $this->{$key} = $attribute;
            }
        }

        $this->username = $this->getEppn();
        $this->salt     = 'hurra-hurra';
        $this->roles    = array();

        $this->setAcademyDomain();
        $this->setAcademy();
        $this->setPassword('knack');

        $saxidUidNumber = $this->getSaxidUidFromUuid('1234567');
        $this->setUidNumber($saxidUidNumber);

        if(!isset($this->displayName)) {
            $displayName = $this->givenName . " " . $this->surname;
            $this->setDisplayName($displayName);
        }
    }

    public function getRoles() {
        return $this->roles;
    }

    public function getSalt() {
        return $this->salt;
    }

    public function getUsername() {
        return $this->username;
    }

    public function eraseCredentials() {
        // do nothing
    }

    public function isEqualTo(UserInterface $user) {
        if (!$user instanceof SaxidUser) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    public function setUid($uid) {
        $this->uid = $uid;
        return $this;
    }

    public function getUid() {
        return $this->uid;
    }

    public function setPassword($password) {
        $this->password = hash('sha512', $password);
        return $this;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setEduPersonPrincipalName($eduPersonPrincipalName) {
        $this->eduPersonPrincipalName = $eduPersonPrincipalName;
        return $this;
    }

    public function getEduPersonPrincipalName() {
        return $this->eduPersonPrincipalName;
    }

    /**
     * Alias for getEduPersonPrincipalName();
     */
    public function getEppn() {
        return $this->getEduPersonPrincipalName();
    }

    public function setEduPersonAffiliation($eduPersonAffiliation) {
        $this->eduPersonAffiliation = $eduPersonAffiliation;
        return $this;
    }

    public function getEduPersonAffiliation($asArray = false) {
        $return = null;
        if(!empty($this->eduPersonAffiliation)) {
            $return = $asArray ? $this->eduPersonAffiliation : implode(', ', $this->eduPersonAffiliation);
        }
        return $return;
    }

    public function setEduPersonPrimaryAffiliation($eduPersonPrimaryAffiliation) {
        $this->eduPersonPrimaryAffiliation = $eduPersonPrimaryAffiliation;
        return $this;
    }

    public function getEduPersonPrimaryAffiliation() {
        return $this->eduPersonPrimaryAffiliation;
    }

    public function setEduPersonScopedAffiliation($eduPersonScopedAffiliation) {
        $this->eduPersonScopedAffiliation = $eduPersonScopedAffiliation;
        return $this;
    }

    public function getEduPersonScopedAffiliation($asArray = false) {
        $return = null;
        if(!empty($this->eduPersonScopedAffiliation)) {
            $return = $asArray ? $this->eduPersonScopedAffiliation : implode(', ', $this->eduPersonScopedAffiliation);
        }
        return $return;
    }

    public function setSurname($surname) {
        $this->surname = $surname;
        return $this;
    }

    public function getSurname() {
        return $this->surname;
    }

    public function setGivenName($givenName) {
        $this->givenName = $givenName;
        return $this;
    }

    public function getGivenName() {
        return $this->givenName;
    }

    public function setUidNumber($uidNumber) {
        $this->uidNumber = $uidNumber;
        return $this;
    }

    public function getUidNumber() {
        return $this->uidNumber;
    }

    public function setEduPersonEntitlement($eduPersonEntitlement) {
        $this->eduPersonEntitlement = $eduPersonEntitlement;
        return $this;
    }

    public function getEduPersonEntitlement() {
        return $this->eduPersonEntitlement;
    }

    public function setOrganizationalUnitName($organizationalUnitName) {
        $this->organizationalUnitName = $organizationalUnitName;
        return $this;
    }

    public function getOrganizationalUnitName() {
        return $this->organizationalUnitName;
    }

    public function setCommonName($commonName) {
        $this->commonName = $commonName;
        return $this;
    }

    public function getCommonName() {
        return $this->commonName;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEduPersonOrgUnitDN($eduPersonOrgUnitDN) {
        $this->eduPersonOrgUnitDN = $eduPersonOrgUnitDN;
        return $this;
    }

    public function getEduPersonOrgUnitDN() {
        return $this->eduPersonOrgUnitDN;
    }

    public function setDisplayName($displayName) {
        $this->displayName = $displayName;
        return $this;
    }

    public function getDisplayName() {
        return $this->displayName;
    }

    public function getGecos() {
        return $this->getDisplayName();
    }

    public function setAcademy() {
        $domain = $this->getAcademyDomain();

        if(array_key_exists($domain, self::$academies)) {
            $return = self::$academies[$domain];
        } else {
            $return = $domain;
        }

        $this->academy = $return;
        return $this;
    }

    public function getAcademy() {
        return $this->academy;
    }

    public function setAcademyDomain() {
        $this->academyDomain = substr(strstr($this->getEduPersonPrincipalName(), '@'), 1);
        return $this;
    }

    public function getAcademyDomain() {
        return $this->academyDomain;
    }

    public function isFromSaxonAcademy() {
        $domain = $this->getAcademyDomain();
        return array_key_exists($domain, self::$academies);
    }

    public function getSaxidUidFromUuid($uuid) {
        if($this->isFromSaxonAcademy()) {
            // Set academy prefix
            $prefix = self::$uidPrefixMapping[$this->getAcademyDomain()];

            // Determine required 0s to fill up
            $numberNulls = 10 - strlen($prefix) - strlen($uuid);

            // Fill with 0s
            $nulls = '';
            for($i = 0; $i <= $numberNulls; $i++) {
                $nulls .= '0';
            }

            // Append UUID
            $saxidUid = $prefix . $nulls . $uuid;
        } else {
            $saxidUid = false;
        }
        
        return $saxidUid;
    }

    public function createLdapDataArray() {
        // Set basic user information
        $data['uid']          = $this->getUid();
        $data['givenName']    = $this->getGivenName();
        $data['sn']           = $this->getSurname();
        $data['cn']           = $this->getCommonName();
        $data['mail']         = $this->getEmail();
        $data['ou']           = $this->getOrganizationalUnitName();
        $data['o']            = $this->getAcademy();
        $data['displayName']  = $this->getDisplayName();
        $data['userPassword'] = "{sha512}".$this->getPassword();

    // eduPerson
        $data['eduPersonAffiliation']        = $this->getEduPersonAffiliation(true);
        // $data['eduPersonAssurance']          = ;
        $data['eduPersonEntitlement']        = $this->getEduPersonEntitlement();
        // $data['eduPersonNickname']           = ;
        // $data['eduPersonOrgDN']              = ;
        $data['eduPersonOrgUnitDN']          = $this->getEduPersonOrgUnitDN();
        $data['eduPersonPrimaryAffiliation'] = $this->getEduPersonPrimaryAffiliation();
        // $data['eduPersonPrimaryOrgUnitDN']   = ;
        $data['eduPersonPrincipalName']      = $this->getEppn();
        $data['eduPersonScopedAffiliation']  = $this->getEduPersonScopedAffiliation(true);
        // $data['eduPersonTargetedID'] = ;

    // SCHAC
        // $data['schacContactLocation'] = ;
        // $data['schacCountryOfCitizenship'] = ;
        // $data['schacCountryOfResidence'] = ;
        // $data['schacDateOfBirth'] = ;
        // $data['schacEmployeeInfo'] = ;
        // $data['schacEntryConfidentiality'] = ;
        // $data['schacEntryMetadata'] = ;
        // $data['schacExperimentalOC'] = ;
        // $data['schacExpiryDate'] = ;
        // $data['schacGender'] = ;
        // $data['schacGroupMembership'] = ;
        // $data['schacHomeOrganization'] = ;
        // $data['schacHomeOrganizationType'] = ;
        // $data['schacLinkageIdentifiers'] = ;
        // $data['schacMotherTongue'] = ;
        // $data['schacPersonalCharacteristics'] = ;
        // $data['schacPersonalPosition'] = ;
        // $data['schacPersonalTitle'] = ;
        // $data['schacPersonalUniqueCode'] = ;
        // $data['schacPersonalUniqueID'] = ;
        // $data['schacPlaceOfBirth'] = ;
        // $data['schacProjectMembership'] = ;
        // $data['schacProjectSpecificRole'] = ;
        // $data['schacSn1'] = ;
        // $data['schacSn2'] = ;
        // $data['schacUserEntitlements'] = ;
        // $data['schacUserPresenceID'] = ;
        // $data['schacUserPrivateAttribute'] = ;
        // $data['schacUserStatus'] = ;

    // dfnEduPerson
        // $data['dfnEduPersonBranchAndDegree'] = ;
        // $data['dfnEduPersonBranchAndType'] = ;
        // $data['dfnEduPersonCostCenter'] = ;
        // $data['dfnEduPersonFeaturesOfStudy'] = ;
        // $data['dfnEduPersonFieldOfStudyString'] = ;
        // $data['dfnEduPersonFinalDegree'] = ;
        // $data['dfnEduPersonStudyBranch1'] = ;
        // $data['dfnEduPersonStudyBranch2'] = ;
        // $data['dfnEduPersonStudyBranch3'] = ;
        // $data['dfnEduPersonTermsOfStudy'] = ;
        // $data['dfnEduPersonTypeOfStudy'] = ;

    // nis/posixAccount
        $data['uidNumber'] = $this->getUidNumber();
        $data['gecos'] = $this->getGecos();
        $data['gidNumber'] = 0;
        $data['homeDirectory'] = '';

    // objectClasses
        // $data['objectclass'][] = 'person';
        $data['objectclass'][] = 'inetOrgPerson';
        $data['objectclass'][] = 'posixAccount';
        $data['objectclass'][] = 'eduPerson';
        $data['objectclass'][] = 'schacPersonalCharacteristics';
        $data['objectclass'][] = 'dfnEduPerson';

        $dn = "cn=".$data['cn'].",o=".$this->getAcademyDomain().",dc=sax-id,dc=de";
        return array('dn' => $dn, 'data' => $data);
    }

    public function __toString() {
        return "{$this->getDisplayName()} ({$this->getAcademy()})";
    }
}