<?php

namespace Saxid\SaxidLdapProxyBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\HttpFoundation\Session\Session;
use \SimpleSAML_Auth_Simple;

use Saxid\SaxidLdapProxyBundle\LdapProxy\SaxidLdapProxy;

class SaxidUserProvider implements UserProviderInterface
{
    private $auth;
    private $session;
    private $ldapHost;
    private $ldapPort;
    private $ldapUser;
    private $ldapPass;

    public function __construct(SimpleSAML_Auth_Simple $auth, Session $session, $ldap_host, $ldap_port, $ldap_user, $ldap_pass) {
        $this->auth     = $auth;
        $this->session  = $session;
        $this->ldapHost = $ldap_host;
        $this->ldapPort = $ldap_port;
        $this->ldapUser = $ldap_user;
        $this->ldapPass = $ldap_pass;
    }

    public function loadUserByUsername($username) {
        $attributes = $this->auth->getAttributes();

        // Create SaxidUser from attributes
        $user = new SaxidUser($attributes);

        return $user;
    }

    public function refreshUser(UserInterface $user) {
        if (!$user instanceof SaxidUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class) {
        return $class === 'Saxid\SaxidLdapProxyBundle\Security\User\SaxidUser';
    }
}
