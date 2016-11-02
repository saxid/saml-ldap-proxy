<?php

namespace Saxid\SaxidLdapProxyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class PasswordController extends Controller
{

    /**
     * @Route("/chpwd", name="saxid_ldap_proxy_password")
     */
    public function chPasswordAction(Request $request)
    {
        
        
//        if(!$this->getUser()->isFromSaxonAcademy()) {
//            $this->addFlash(
//                'danger',
//                'You have to be a member of a Saxon academy in order to persist User to LDAP'
//            );
//        } else {
//
//          $slp = $this->get('saxid_user_provider');
//          #$status = $slp->getStatus();
//          $saxuser = $slp->loadUserByUsername("tester");
//          $usrn = $saxuser->getUsername();
//          $pw = $saxuser->getPassword();
//
//          $this->addFlash(
//              "info",
//              "usr: " . $usrn . " pw: " . $pw
//          );
//
//        }
//
//        #$hpc = $this->get('saxid_ldap_proxy.hpc');
//        #$pwdm = $this->get('saxid_ldap_proxy.pwdm');
//
//        #$pwdm->changePassword($form->get('newPassword')->getData());
//        $this->addFlash(
//            'info',
//            'Password successfully changed' );
        
        
        //"newPassword"
//        print "DUMP:";
//        var_dump($request);
//        print "</br>";
        
        print "DUMP </br>";
            //var_dump($request->get('newPassword'));
        print $request->get('newPassword');
        print $request->get('newPasswordCheck');
        //dump($request->request->get('newPassword'));
        //dump($request->request->get('newPasswordCheck'));
        
        //$value = $request->query->get("newPassword");
        //print "Password: " . $value;
        $zeit = time();
        print "</br>MY PASSWORD PAGE TEST" . $zeit;

        return $this->render('SaxidLdapProxyBundle::password.html.twig');
    }

    /**
     * @Route("/chpwd/{$userPassword})
     *
     */
    public function deleteActionName($userPassword)
    {
        
    }

    public function chPassword1Action()
    {
        print "chPasswordAction1";
        return $this->render('SaxidLdapProxyBundle::user.html.twig');
    }
}