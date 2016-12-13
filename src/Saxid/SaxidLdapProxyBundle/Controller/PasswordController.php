<?php

namespace Saxid\SaxidLdapProxyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Saxid\SaxidLdapProxyBundle\LdapProxy\SaxidLdapProxy;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

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

        // Get User Object
        /* @var $saxidUser \Saxid\SaxidLdapProxyBundle\Security\User\SaxidUser */
        $saxidUser = $this->getUser();

        $form = $this->createFormBuilder($saxidUser)
            ->add('password', RepeatedType::class, array(
              'type' => PasswordType::class,
              'invalid_message' => 'Die Passwörter müssen übereinstimmen.',
              'options' => array('attr' => array('class' => 'form-control')),
              'required' => true,
              'first_options'  => array('label' => 'Passwort'),
              'second_options' => array('label' => 'Passwort wiederholen')
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Password ändern',
                'attr' => array(
                  'class' => 'btn btn-primary'
                )
            ))
            ->add('generate', SubmitType::class, array(
                'label' => 'Password erzeugen',
                'attr' => array(
                  'class' => 'btn btn-default'
                )
            ))
            ->getForm();

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
              $saxLdap->disconnect();
              $this->addFlash($status['type'], $status['message']);

            }

            if ($form->get('save')->isClicked() && $form->isValid()) {
                $data = $form->getData();

                // Set password
                $saxLdap->setUserPassword($saxidUser->createLdapUserDN(), $data->getPassword() );

                // Get status
                $status = $saxLdap->getStatus();

                // Close connection
                $saxLdap->disconnect();

                // Add status message to Symfony flashbag
                $this->addFlash($status['type'], $status['message']);

                return $this->redirectToRoute('saxid_ldap_proxy_password');
              }

        return $this->render('SaxidLdapProxyBundle::password.html.twig', array(
            'hpcform' => $form->createView(),
        ));
    }
}
