<?php

namespace Saxid\SaxidLdapProxyBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use \SimpleSAML_Auth_Simple;

class SaxidUserProvider implements UserProviderInterface
{
    private $auth;

    public function __construct(SimpleSAML_Auth_Simple $auth) {
        $this->auth = $auth;
    }

    public function loadUserByUsername($username) {
        $attributes = $this->auth->getAttributes();
        return new SaxidUser($attributes);
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