<?php

namespace Saxid\SaxidLdapProxyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Saxid\SaxidLdapProxyBundle\LdapProxy\SaxidLdapProxy;

class PasswordController extends Controller
{

    /**
     * @Route("/chpwd", name="saxid_ldap_proxy_password")
     */
    public function chPasswordAction(Request $request)
    {
        // Check if from saxony
        if (!$this->getUser()->isFromSaxonAcademy())
        {
            $this->addFlash('danger', 'You have to be a member of a Saxon academy in order to persist User to LDAP');
            return $this->render('SaxidLdapProxyBundle::password.html.twig');
        }

        // Check if a button is pressed
        if (is_null($request->get('btnChange')) && is_null($request->get('btnGenerate')))
        {
            return $this->render('SaxidLdapProxyBundle::password.html.twig');
        }

        $passwordToChange;
        // Set new password
        if (!is_null($request->get('btnChange')))
        {
            if (empty($request->get('newPassword')) || empty($request->get('newPasswordCheck')))
            {
                $this->addFlash("danger", "Error - Passwords are empty.");
                return $this->render('SaxidLdapProxyBundle::password.html.twig');
            }
            
            if ($request->get('newPassword') != $request->get('newPasswordCheck'))
            {
                $this->addFlash("danger", "Error - Passwords are different.");
                return $this->render('SaxidLdapProxyBundle::password.html.twig');
            }

            $passwordToChange = $request->get('newPassword');
        }
        // Generate new password
        else if (!is_null($request->get('btnGenerate')))
        {
            $this->addFlash("danger", "NOT IMPLEMENTED YET");
            return $this->render('SaxidLdapProxyBundle::password.html.twig');

            //TODO: Routine zum Passwort generieren
        }

        // Obtain LDAP credentials
        $ldapHost = $this->container->getParameter('ldap_host');
        $ldapPort = $this->container->getParameter('ldap_port');
        $ldapUser = $this->container->getParameter('ldap_user');
        $ldapPass = $this->container->getParameter('ldap_pass');
        $baseDN = $this->container->getParameter('baseDN');

        // Get User Object
        /* @var $saxidUser \Saxid\SaxidLdapProxyBundle\Security\User\SaxidUser */
        $saxidUser = $this->getUser();

        // Create LDAP Access Object
        $saxLdap = new SaxidLdapProxy($ldapHost, $ldapPort, $ldapUser, $ldapPass, $baseDN);

        // Connect to LDAP
        $saxLdap->connect();

        // Set password
        $saxLdap->setUserPassword($saxidUser->createLdapUserDN(), $passwordToChange);

        // Get status
        $status = $saxLdap->getStatus();

        // Close connection
        $saxLdap->disconnect();

        // Add status message to Symfony flashbag
        $this->addFlash($status['type'], $status['message']);

        return $this->render('SaxidLdapProxyBundle::password.html.twig');
    }
}