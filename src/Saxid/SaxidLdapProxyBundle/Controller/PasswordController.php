<?php

namespace Saxid\SaxidLdapProxyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PasswordController extends Controller
{
    /**
    * @Route("/chpwd", name="saxid_ldap_proxy_password")
    */
    public function chPasswordAction()
    {
        if(!$this->getUser()->isFromSaxonAcademy()) {
            $this->addFlash(
                'danger',
                'You have to be a member of a Saxon academy in order to persist User to LDAP'
            );
        } else {

          $slp = $this->get('saxid_user_provider');
          #$status = $slp->getStatus();
          $saxuser = $slp->loadUserByUsername("tester");
          $usrn = $saxuser->getUsername();
          $pw = $saxuser->getPassword();

          $this->addFlash(
              "info",
              "data - " . "usr: " . $usrn . " pw: " . $pw
          );

        }

        #$hpc = $this->get('saxid_ldap_proxy.hpc');
        #$pwdm = $this->get('saxid_ldap_proxy.pwdm');

        #$pwdm->changePassword($form->get('newPassword')->getData());
        $this->addFlash(
            'info',
            'Password successfully changed' );

        return $this->render('SaxidLdapProxyBundle::password.html.twig');
    }
}
