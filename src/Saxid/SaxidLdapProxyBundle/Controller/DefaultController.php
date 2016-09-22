<?php

namespace Saxid\SaxidLdapProxyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Saxid\SaxidLdapProxyBundle\LdapProxy\SaxidLdapProxy;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        if(!$session->get('status')) {
            // Obtain LDAP credentials
            $ldapHost = $this->container->getParameter('ldap_host');
            $ldapPort = $this->container->getParameter('ldap_port');
            $ldapUser = $this->container->getParameter('ldap_user');
            $ldapPass = $this->container->getParameter('ldap_pass');

            // Run the LDAP proxy
            $slp    = new SaxidLdapProxy($this->getUser(), $ldapHost, $ldapPort, $ldapUser, $ldapPass);
            $status = $slp->getStatus();

            // Add status message to Symfony flashbag
            $this->addFlash(
                $status['type'],
                $status['message']
            );

            $session->set('status', 'DONE');
        } else {
            if(!$this->getUser()->isFromSaxonAcademy()) {
                $this->addFlash(
                    'danger',
                    'You have to be a member of a Saxon academy in order to persist User to LDAP'
                );
            }
        }

        $this->addFlash(
            'info',
            'Added Passwordpage and Encryption of Passwords to MD5 (insecure!!): '.$session->get('status')
        );

        return $this->render('SaxidLdapProxyBundle:Default:index.html.twig');
    }
}
