<?php

namespace Saxid\SaxidLdapProxyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Saxid\SaxidLdapProxyBundle\Exception\UserIsNoMemberOfSaxonAcademyException;

class LdapController extends Controller
{
    public function adduserAction()
    {
        if(!$this->getUser()->isFromSaxonAcademy()) {
            throw new UserIsNoMemberOfSaxonAcademyException('You have to be a member of a Saxon academy in order to persist User to LDAP');
        }

    	$slp = $this->get('saxid_ldap_proxy');
    	list($message, $status) = $slp->getStatus();

    	$this->addFlash(
    		$status,
    		$message
    	);

        return $this->render('SaxidLdapProxyBundle:Ldap:adduser.html.twig', array());
    }
}
