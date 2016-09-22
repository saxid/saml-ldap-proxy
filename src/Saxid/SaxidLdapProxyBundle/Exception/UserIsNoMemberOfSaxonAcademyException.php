<?php

namespace Saxid\SaxidLdapProxyBundle\Exception;

/**
 * UserIsNoMemberOfSaxonAcademyException
 * 
 * thrown if User tries to perform an action that is restricted
 * to members of Saxon academies
 *
 * @author Moritz Hesse <moritz.hesse@tu-dresden.de>
 */
class UserIsNoMemberOfSaxonAcademyException extends \Exception
{
    public function __construct($message, $code = 403) {
        parent::__construct($message, $code);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}