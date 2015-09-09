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

    public function __construct(SimpleSAML_Auth_Simple $auth, Session $session) {
        $this->auth    = $auth;
        $this->session = $session;
    }

    public function loadUserByUsername($username) {
        $attributes = $this->auth->getAttributes();

        // Create SaxidUser from attributes
        $user = new SaxidUser($attributes);

        // Run the LDAP proxy
        $slp    = new SaxidLdapProxy($user);
        $status = $slp->getStatus();

        // Add status message to Symfony flashbag
        $this->session->getFlashBag()->add(
            $status['type'],
            $status['message']
        );

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