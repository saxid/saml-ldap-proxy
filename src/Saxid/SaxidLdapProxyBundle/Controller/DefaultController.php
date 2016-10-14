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
        if (!$session->get('status'))
        {
            // Obtain LDAP credentials
            $ldapHost = $this->container->getParameter('ldap_host');
            $ldapPort = $this->container->getParameter('ldap_port');
            $ldapUser = $this->container->getParameter('ldap_user');
            $ldapPass = $this->container->getParameter('ldap_pass');
            $baseDN = $this->container->getParameter('baseDN');

            // Get User Object
            /* @var $saxidUser \Saxid\SaxidLdapProxyBundle\Security\User\SaxidUser */
            $saxidUser = $this->getUser();

            //Create LDAP Access Object
            $saxLdap = new SaxidLdapProxy($ldapHost, $ldapPort, $ldapUser, $ldapPass, $baseDN);
            
            // Connect to LDAP
            $saxLdap->connect();

            // Check if user already exists in LDAP
            if ($saxLdap->existsUser("uid=" . $saxidUser->getUid()))
            {
                // Modify entry
                $saxLdap->modifyUser($saxidUser->createLdapUserDN(), $saxidUser->createLdapDataArray());
            }
            else
            {
                // Add new entry
                $saxLdap->addUser($saxidUser->createLdapUserDN(), $saxidUser->createLdapDataArray(true));
                $saxLdap->setUserPassword($saxidUser->createLdapUserDN(), "knack");
            }

            // Get status
            $status = $saxLdap->getStatus();

            // Close connection
            $saxLdap->disconnect();

            // Add status message to Symfony flashbag
            $this->addFlash($status['type'], $status['message']);
            $session->set('status', 'DONE');
        }
        else
        {
            if (!$this->getUser()->isFromSaxonAcademy())
            {
                $this->addFlash('danger', 'You have to be a member of a Saxon academy in order to persist User to LDAP');
            }
        }

        //$this->addFlash('info', 'Added Passwordpage and Encryption of Passwords to MD5 (insecure!!): ' . $session->get('status'));

        return $this->render('SaxidLdapProxyBundle:Default:index.html.twig');
    }
}