<?php

namespace Saxid\SaxidLdapProxyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        if(!$this->getUser()->isFromSaxonAcademy()) {
            $this->addFlash(
                'danger',
                'You have to be a member of a Saxon academy in order to persist User to LDAP'
            );
        }
        
        return $this->render('SaxidLdapProxyBundle:Default:index.html.twig');
    }
}
