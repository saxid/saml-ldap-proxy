<?php

namespace Saxid\SaxidLdapProxyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Saxid\SaxidLdapProxyBundle\Services\SaxIDAPI;
use Saxid\SaxidLdapProxyBundle\LdapProxy\SaxidLdapProxy;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        // todo auth check
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $session = $request->getSession();
        $logger = $this->get('logger');

        if (!$this->getUser()->isFromSaxonAcademy())
        {
            $this->addFlash('danger', 'You have to be a member of a Saxon academy in order to persist User to LDAP');
        }

        // redirect if TOS not yet accepted
        if ( empty($session->get('tosyes')) )
        {
          // redirect to the "homepage" route
          return $this->redirectToRoute('saxid_ldap_proxy_tos');
        }

        if (empty($session->get('status')) && $session->get('tosyes') == 'DONE')
        {
          // Get User Object
          /* @var $saxidUser \Saxid\SaxidLdapProxyBundle\Security\User\SaxidUser */
          $saxidUser = $this->getUser();

          //Create LDAP Access Object
          $saxLdap = $this->get('saxid_ldap_proxy');

          // Connect to LDAP
          $saxLdap->connect();

          // Check if user already exists in LDAP
          if ($saxLdap->userExist("uid=" . $saxidUser->getUid()))
          {
              // Modify entry
              $saxLdap->modifyLDAPObject($saxidUser->createLdapUserDN($this->getParameter('ldap_baseDN')), $saxidUser->createLdapDataArray());
              $logger->info('User Modified in LDAP: '. $saxidUser->getUid());

          }
          else
          {
              // Add new entry
              // When organization doesn't exists -> create
              if ($saxLdap->existsOrganization($saxidUser->createLdapOrganizationDN($this->getParameter('ldap_baseDN'))) == FALSE)
              {
                  //objectclasses
                  $organizationData['objectclass'][] = 'organization';
                  $organizationData['objectclass'][] = 'saxID';
                  $organizationData['objectclass'][] = 'top';

                  //attributes
                  $organizationData["lastUserUIDNumber"] = "1";
                  $uidNumberPrefix = $saxLdap->getLastAcademyUIDNumber($this->getParameter('ldap_baseDN')) + 1;
                  $organizationData["uidNumberPrefix"] = $uidNumberPrefix;

                  //add organization
                  $saxLdap->addLDAPObject($saxidUser->createLdapOrganizationDN($this->getParameter('ldap_baseDN')), $organizationData);

                  //set last uidNumber at top domain
                  $saxLdap->setLastAcademyUIDNumber($this->getParameter('ldap_baseDN'), $uidNumberPrefix);
              }

              $tmpLastUserUIDNumber = $saxLdap->getLastUserUIDNumber($saxidUser->createLdapOrganizationDN($this->getParameter('ldap_baseDN')));
              $tmpAcademyPrefix = $saxLdap->getUIDNumberPrefix($saxidUser->createLdapOrganizationDN($this->getParameter('ldap_baseDN')));

              // Create unique UIDNumber for user
              for ($index = 0; $index < 100; $index++)
              {
                  $tmpUidNumber = $saxidUser->generateSaxIDUIDNumber($tmpAcademyPrefix, $tmpLastUserUIDNumber + 1);

                  // if user not exists in ldap set a new UID Num
                  if ($saxLdap->userExist("uidNumber=" . $tmpUidNumber) == FALSE)
                  {
                      // Set UIDNumber
                      $saxidUser->setUidNumber($tmpUidNumber);
                      break;
                  }
              }

              // Add user to ldap
              $saxLdap->addLDAPObject($saxidUser->createLdapUserDN($this->getParameter('ldap_baseDN')), $saxidUser->createLdapDataArray(true));
              $logger->info('User Added to LDAP: '. $saxidUser->getUid());
              // modify lastUserUIDNumber
              $saxLdap->setLastUserUIDNumber($saxidUser->createLdapOrganizationDN($this->getParameter('ldap_baseDN')), ($tmpLastUserUIDNumber + 1));

              // generate user password
              $initialPassword = $saxidUser->generateRandomPassword();
              $this->addFlash("warning", "Dein initiales Service-Passwort (bitte merken bzw. ändern unter 'Mein Konto'): " . $initialPassword);
              $saxLdap->setUserPassword($saxidUser->createLdapUserDN($this->getParameter('ldap_baseDN')), $initialPassword);

              // Add user to SaxIDAPI
              $sa = $this->get('saxid_ldap_proxy.saxapi');

              $format = 'Y-m-d\TH:i:s\Z';
              $expiryDate = date($format, mktime(0, 0, 0, date('m'), date('d') + 365));
              $deletionDate = date($format, mktime(0, 0, 0, date('m'), date('d') + 365 + 30));

              $sa->createAPIEntry($saxidUser->getEduPersonPrincipalName(), $deletionDate, $expiryDate);

              $logger->info('API Info: '. $saxidUser->getEduPersonPrincipalName() . ' added.');
              //$logger->error('An error occurred');
              //$logger->critical('I left the oven on!', array(
                  // include extra "context" info in your logs
              //    'cause' => 'in_hurry',
              //));
          }

          // Get status
          //$status = $saxLdap->getStatus();

          // Close connection
          $saxLdap->disconnect();

          // Add status message to Symfony flashbag
          //$this->addFlash($status['type'], $status['message']);
          $this->addFlash('success', 'Hallo ' . $saxidUser->getSurname() . '. Deine Attribute vom Identityprovider wurden erfolgreich in die Datenbank übertragen. Ein Service-Passwort haben wir angelegt.');
          // set init user check and write to do this only once per page load
          $session->set('status', 'DONE');
        }

        return $this->render('SaxidLdapProxyBundle:Default:index.html.twig');

    }

    public function startAction(Request $request)
    {
        $session = $request->getSession();
        //Create LDAP Access Object
        $saxLdap = $this->get('saxid_ldap_proxy');
        // Connect to LDAP
        $saxLdap->connect();

        $userexist = $saxLdap->userExist("uid=" . $this->getUser()->getUid());

        if ( !empty($userexist) ){
          $session->set('Ldapuser', '1');
          // Modify entry
          $saxLdap->modifyLDAPObject($this->getUser()->createLdapUserDN($this->getParameter('ldap_baseDN')), $this->getUser()->createLdapDataArray());
          $this->addFlash('info', 'Willkommen zurück ' . $this->getUser()->getGivenName() . '! Deine Attribute wurden erfolgreich aktualisiert. Details findest du unter Mein Konto im Menu oben.');
        }

        // Close connection
        $saxLdap->disconnect();

        //check if user accepted TOS or User already persistend in LDAP DB
        if ($session->get('tosyes') == 'DONE' || !empty($session->get('Ldapuser')) )
        {
            if (!$this->getUser()->isFromSaxonAcademy())
            {
                $this->addFlash('danger', 'You have to be a member of a Saxon academy in order to persist User to LDAP');
            }
            return $this->render('SaxidLdapProxyBundle:Default:index.html.twig');
        }

        $form = $this->createFormBuilder()
                ->add('ok', SubmitType::class, array(
                    'label' => 'Zustimmen und Weiter',
                    'attr' => array(
                        'class' => 'btn btn-primary'
                    )
                ))
                ->add('cancel', SubmitType::class, array(
                    'label' => 'Ablehnen und Verlassen',
                    'attr' => array(
                        'class' => 'btn btn-default'
                    )
                ))
                ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->get('ok')->isClicked())
        {
            $session->set('tosyes', 'DONE');
            return $this->redirectToRoute('saxid_ldap_proxy_homepage');
        }
        elseif ($form->get('cancel')->isClicked())
        {
            // Logout
            $auth = $this->get('simplesamlphp.auth');
            return $this->redirect($auth->getLogoutURL());
        }

        return $this->render('SaxidLdapProxyBundle:Default:termsofservice.html.twig', array(
                    'tosform' => $form->createView(),
        ));
    }
}
