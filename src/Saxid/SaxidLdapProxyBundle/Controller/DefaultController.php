<?php

namespace Saxid\SaxidLdapProxyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	$logout = $this->get('simplesamlphp.auth')->getLogoutURL();

        return $this->render('SaxidLdapProxyBundle:Default:index.html.twig', array(
        	'user' => $this->getUser(),
        	'logout' => $logout
        ));
    }
}
