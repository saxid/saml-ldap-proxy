<?php

namespace Saxid\SaxidLdapProxyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
}
