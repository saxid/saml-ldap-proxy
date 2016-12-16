<?php

namespace Saxid\SaxidLdapProxyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Saxid\SaxidLdapProxyBundle\LdapProxy\SaxidLdapProxy;
use Symfony\Component\HttpFoundation\Request;
use \Saxid\SaxidLdapProxyBundle\Services\SaxIDAPI;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        $session = $request->getSession();

        if ($session->get('status'))
        {
            if (!$this->getUser()->isFromSaxonAcademy())
            {
                $this->addFlash('danger', 'You have to be a member of a Saxon academy in order to persist User to LDAP');
            }
            return $this->render('SaxidLdapProxyBundle:Default:index.html.twig');
        }

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

                if ($saxLdap->existsUser("uidNumber=" . $tmpUidNumber) == FALSE)
                {
                    // Set UIDNumber
                    $saxidUser->setUidNumber($tmpUidNumber);
                    break;
                }
            }

            // Add
            $saxLdap->addLDAPObject($saxidUser->createLdapUserDN(), $saxidUser->createLdapDataArray(true));

            // modify lastUserUIDNumber
            $saxLdap->setLastUserUIDNumber($saxidUser->createLdapOrganizationDN(), ($tmpLastUserUIDNumber + 1));

            // generate user password
            $initialPassword = $saxidUser->generateRandomPassword();
            $this->addFlash("info", "Initial service password: " . $initialPassword);
            $saxLdap->setUserPassword($saxidUser->createLdapUserDN(), $initialPassword);

            // Add user to SaxIDAPI
            $SaxIDApiAccess = new SaxIDAPI("https://saxid-api.zih.tu-dresden.de/api/", "9fb68218567edb66a7f5ce5e2f916da89b7fc7e5");

            $format = 'Y-m-d\TH:i:s\Z';
            $expiryDate = date($format, mktime(0, 0, 0, date('m'), date('d') + 365));
            $deletionDate = date($format, mktime(0, 0, 0, date('m'), date('d') + 365 + 30));

            $SaxIDApiAccess->createAPIEntry($saxidUser->getEduPersonPrincipalName(), "076f2d546d034c8f923c9bb76aa37c9e", $deletionDate, $expiryDate);
        }

        // Get status
        $status = $saxLdap->getStatus();

        // Close connection
        $saxLdap->disconnect();

        // Add status message to Symfony flashbag
        $this->addFlash($status['type'], $status['message']);
        // set init user check and write to do this only once per page load
        $session->set('status', 'DONE');

        return $this->render('SaxidLdapProxyBundle:Default:index.html.twig');
    }

    public function startAction(Request $request)
    {
        $session = $request->getSession();

        if ($session->get('status'))
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
                        'class' => 'btn btn-default'
                    )
                ))
                ->add('cancel', SubmitType::class, array(
                    'label' => 'Ablehnen und Verlassen',
                    'attr' => array(
                        'class' => 'btn btn-primary'
                    )
                ))
                ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->get('ok')->isClicked())
        {
            $session->set('status', 'DONE');
            return $this->redirectToRoute('saxid_ldap_proxy_homepage');
        }
        elseif ($form->get('cancel')->isClicked())
        {
            // Logout
            return $this->redirectToRoute('logout');
        }

        return $this->render('SaxidLdapProxyBundle:Default:termsofservice.html.twig', array(
                    'tosform' => $form->createView(),
        ));
    }
}