<?php

namespace Saxid\SaxidLdapProxyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Saxid\SaxidLdapProxyBundle\Security\User\SaxidUser;
use Saxid\SaxidLdapProxyBundle\Exception\UserIsNoMemberOfSaxonAcademyException;

class LdapController extends Controller
{
    public function adduserAction()
    {
        if(!$this->getUser()->isFromSaxonAcademy()) {
            $this->addFlash(
                'danger',
                'You have to be a member of a Saxon academy in order to persist User to LDAP'
            );
        } else {
            $slp = $this->get('saxid_ldap_proxy');
            $status = $slp->getStatus();

            $this->addFlash(
                $status['type'],
                $status['message']
            );
        }

        return $this->render('SaxidLdapProxyBundle:Default:index.html.twig');
    }

    public function readuserAction()
    {
        if(!$this->getUser()->isFromSaxonAcademy()) {
            $this->addFlash(
                'danger',
                'You have to be a member of a Saxon academy in order to persist User to LDAP'
            );
        } else {
            $slp = $this->get('saxid_ldap_proxy');

            $saxidUser = $this->getUser();

            $slp->connect();

            $ldapuser = $slp->getLdapUser("uid=" . $saxidUser->getUid());

            $status = $slp->getStatus();

            $slp->disconnect();

            $this->addFlash(
                $status['type'],
                $status['message']
            );
        }

        return $this->render('SaxidLdapProxyBundle::user.html.twig', array('ldapuser' => $ldapuser));
    }
}
