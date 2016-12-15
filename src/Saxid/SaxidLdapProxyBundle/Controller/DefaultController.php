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

            //TODO
            $initialPassword = $saxidUser->generateRandomPassword();
            $this->addFlash("info", "Initial service password: " . $initialPassword);
            $saxLdap->setUserPassword($saxidUser->createLdapUserDN(), $initialPassword);
        }

        // Get status
        $status = $saxLdap->getStatus();

        // Close connection
        $saxLdap->disconnect();

        // Add status message to Symfony flashbag
        $this->addFlash($status['type'], $status['message']);
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

            if ($form->get('agree')->isClicked())
            {
              return $this->forward('SaxidLdapProxyBundle:Default:index.html.twig');

            } elseif($form->get('decline')->isClicked()){
              // Logout
            }
        }

    }

    private function FillWithZeros($value)
    {
        if (count_chars($value) == 1)
        {
            return "00" . $value;
        }
        else if (count_chars($value) == 2)
        {
            return "0" . $value;
        }
        else
        {
            return $value;
        }
    }
}
