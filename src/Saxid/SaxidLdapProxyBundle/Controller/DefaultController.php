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
          if ($saxLdap->existsUser("uid=" . $saxidUser->getUid()))
          {
              // Modify entry
              $saxLdap->modifyLDAPObject($saxidUser->createLdapUserDN(), $saxidUser->createLdapDataArray());
              $logger->info('User Modified in LDAP: '. $saxidUser->getUid());
          }
          else
          {
              // Add new entry
              // When organization doesn't exists -> create
              if ($saxLdap->existsOrganization($saxidUser->createLdapOrganizationDN()) == FALSE)
              {
                  //objectclasses
                  $organizationData['objectclass'][] = 'organization';
                  $organizationData['objectclass'][] = 'saxID';
                  $organizationData['objectclass'][] = 'top';

                  //attributes
                  $organizationData["lastUserUIDNumber"] = "1";
                  $uidNumberPrefix = $saxLdap->getLastAcademyUIDNumber("dc=sax-id,dc=de") + 1;
                  $organizationData["uidNumberPrefix"] = $uidNumberPrefix;

                  //add organization
                  $saxLdap->addLDAPObject($saxidUser->createLdapOrganizationDN(), $organizationData);

                  //set last uidNumber at top domain
                  $saxLdap->setLastAcademyUIDNumber("dc=sax-id,dc=de", $uidNumberPrefix);
              }

              $tmpLastUserUIDNumber = $saxLdap->getLastUserUIDNumber($saxidUser->createLdapOrganizationDN());
              $tmpAcademyPrefix = $saxLdap->getUIDNumberPrefix($saxidUser->createLdapOrganizationDN());

              // Create unique UIDNumber for user
              for ($index = 0; $index < 100; $index++)
              {
                  $tmpUidNumber = $saxidUser->generateSaxIDUIDNumber($tmpAcademyPrefix, $tmpLastUserUIDNumber + 1);

                  // if user not exists in ldap set a new UID Num
                  if ($saxLdap->existsUser("uidNumber=" . $tmpUidNumber) == FALSE)
                  {
                      // Set UIDNumber
                      $saxidUser->setUidNumber($tmpUidNumber);
                      break;
                  }
              }

              // Add user to ldap
              $saxLdap->addLDAPObject($saxidUser->createLdapUserDN(), $saxidUser->createLdapDataArray(true));
              $logger->info('User Added to LDAP: '. $saxidUser->getUid());
              // modify lastUserUIDNumber
              $saxLdap->setLastUserUIDNumber($saxidUser->createLdapOrganizationDN(), ($tmpLastUserUIDNumber + 1));

              // generate user password
              $initialPassword = $saxidUser->generateRandomPassword();
              $this->addFlash("info", "Initial service password: " . $initialPassword);
              $saxLdap->setUserPassword($saxidUser->createLdapUserDN(), $initialPassword);

              // Add user to SaxIDAPI
              $sa = $this->get('saxid_ldap_proxy.saxapi');

              $format = 'Y-m-d\TH:i:s\Z';
              $expiryDate = date($format, mktime(0, 0, 0, date('m'), date('d') + 365));
              $deletionDate = date($format, mktime(0, 0, 0, date('m'), date('d') + 365 + 30));

              $sa->createAPIEntry($saxidUser->getEduPersonPrincipalName(), $deletionDate, $expiryDate);

              $logger->info('API Entry Info for user: '. $saxidUser->getEduPersonPrincipalName() . ' added.');
              //$logger->error('An error occurred');
              //$logger->critical('I left the oven on!', array(
                  // include extra "context" info in your logs
              //    'cause' => 'in_hurry',
              //));
          }

          // Get status
          $status = $saxLdap->getStatus();

          // Close connection
          $saxLdap->disconnect();

          // Add status message to Symfony flashbag
          $this->addFlash($status['type'], $status['message']);
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

        $userexists = $saxLdap->existsUser("uid=" . $this->getUser()->getUid());
        // Close connection
        $saxLdap->disconnect();

        if ( !empty($userexists) ){
          $session->set('Ldapuser', '1');
        }

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
