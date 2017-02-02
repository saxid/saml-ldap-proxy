<?php

namespace Saxid\SaxidLdapProxyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Saxid\SaxidLdapProxyBundle\Security\User\SaxidUser;
use Saxid\SaxidLdapProxyBundle\Exception\UserIsNoMemberOfSaxonAcademyException;

class LdapController extends Controller
{
    public function adduserAction(Request $request)
    {
      $session = $request->getSession();
      if ($session->get('tosyes') != 'DONE' )
      {
        // redirect to the "homepage" route
        return $this->redirectToRoute('saxid_ldap_proxy_tos');
      }

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

    public function readuserAction(Request $request)
    {
      $session = $request->getSession();
      if ( empty($session->get('tosyes')) && empty($session->get('Ldapuser')))
      {
        // redirect to the "homepage" route
        return $this->redirectToRoute('saxid_ldap_proxy_tos');
      } else {

      }

      if(!$this->getUser()->isFromSaxonAcademy()) {
          $this->addFlash(
              'danger',
              'You have to be a member of a Saxon academy in order to persist User to LDAP'
          );
      } else {
          $slp = $this->get('saxid_ldap_proxy');

          $saxidUser = $this->getUser();

          $slp->connect();

          // gets user-data from LDAP and returns the LdapUser-Class
          $ldapuser = $slp->getLdapUser("uid=" . $saxidUser->getUid());

          //$status = $slp->getStatus();

          $slp->disconnect();

          $this->addFlash( "info", "Wir haben dich in der Datenbank gefunden. Dein Passwort kannst du unter Mein Konto ändern."
              //$status['type'],
              //$status['message']
          );
      }

      return $this->render('SaxidLdapProxyBundle::user.html.twig', array('ldapuser' => $ldapuser));
    }
}
