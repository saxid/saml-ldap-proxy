<?php

namespace Saxid\SaxidLdapProxyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Saxid\SaxidLdapProxyBundle\LdapProxy\SaxidLdapProxy;
use Saxid\SaxidLdapProxyBundle\Form\UserPasswordType;

class PasswordController extends Controller
{

    /**
     * @Route("/chpwd", name="saxid_ldap_proxy_password")
     */
    public function chPasswordAction(Request $request)
    {
      $session = $request->getSession();
      //redirect if user didn't accept tos
      if (empty($session->get('tosyes')) && empty($session->get('Ldapuser')) )
      {
        // redirect to the "homepage" route
        return $this->redirectToRoute('saxid_ldap_proxy_tos');
      }

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

      // Get User Object
      /* @var $saxidUser \Saxid\SaxidLdapProxyBundle\Security\User\SaxidUser */
      $saxidUser = $this->getUser();

      // get LDAP Access Object
      $saxLdap = $this->get('saxid_ldap_proxy');

      // Connect to LDAP
      $saxLdap->connect();

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
          $passwordToChange = $saxidUser->generateRandomPassword();
          $this->addFlash("info", "Generated service password: " . $passwordToChange);
      }

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

    public function newAction(Request $request)
    {
      $session = $request->getSession();
      if ( empty($session->get('tosyes')) && empty($session->get('Ldapuser')))
      {
        // redirect to the "homepage" route
        return $this->redirectToRoute('saxid_ldap_proxy_tos');
      } else {

      }
      // Get User Object
      /* @var $saxidUser \Saxid\SaxidLdapProxyBundle\Security\User\SaxidUser */
      $saxidUser = $this->getUser();
      // create the user password-form of type userpassword
      // and take the saxidUser class as data_class to map attributes via getters/setters
      $form = $this->createForm(UserPasswordType::class, $saxidUser);

      $form->handleRequest($request);

      // Create LDAP Access Object
      $saxLdap = $this->get('saxid_ldap_proxy');

      // Connect to LDAP
      $saxLdap->connect();

      if ($form->get('generate')->isClicked()) {

          $newPass = $saxidUser->generateRandomPassword();
          $this->addFlash("info", "Generated service password: " . $newPass);
          $saxLdap->setUserPassword($saxidUser->createLdapUserDN(), $newPass );
          $status = $saxLdap->getStatus();
          $this->addFlash($status['type'], $status['message']);

      }

      if ($form->get('save')->isClicked() && $form->isValid()) {

          $data = $form->getData();
          // Set password
          $saxLdap->setUserPassword($saxidUser->createLdapUserDN(), $data->getPassword() );
          // Get ldap-status
          $status = $saxLdap->getStatus();
          // Add status message to Symfony flashbag
          $this->addFlash($status['type'], $status['message']);

          return $this->redirectToRoute('saxid_ldap_proxy_password');
      }

      // Close connection
      $saxLdap->disconnect();

      return $this->render('SaxidLdapProxyBundle::password.html.twig', array(
          'hpcform' => $form->createView(),
      ));
    }
}
