<?php

namespace Saxid\SaxidLdapProxyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use \Saxid\SaxidLdapProxyBundle\Services\SaxIDAPI;

class TestAPIController extends Controller
{

    public function queryapiAction()
    {
        $sapi = $this->get('saxid_ldap_proxy.saxapi');

        $as = $sapi->getServices();
        $ar = $sapi->getRessources();
        foreach ($ar as $key => $value) {
          $ret[$value['eppn']] = $value;
        }
        ksort($ret, SORT_STRING);

        // Connect to LDAP and get Users
        $slp = $this->get('saxid_ldap_proxy');
        $slp->connect();
        $ldapdata = $slp->getAllLdapUser();
        $slp->disconnect();
        foreach ($ldapdata as $luser) {
          $tmparr2[] = $luser->getUid() . '|' . $luser->getDn();
        }
        dump($tmparr2);

        return $this->render('SaxidLdapProxyBundle::testAPI.html.twig', array( 'apiservices' => $as, 'apiresources' => $ret ) );
    }

    //TEST
    function curlsfunc()
    {
        $ch = curl_init();
        $header = array('Authorization: Token 9fb68218567edb66a7f5ce5e2f916da89b7fc7e5');

        curl_setopt($ch, CURLOPT_URL, "https://saxid-api.zih.tu-dresden.de/api/services/"); # URL to post to
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); # return into a variable
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); # custom headers, see above

        if (!$result = curl_exec($ch))
        {
            print "Error: </br>";
            //dump(curl_error($ch));
            //trigger_error(curl_error($ch));
        }

        curl_close($ch);

        $myVar = json_decode($result);
        print "Result: </br>";
        //dump($myVar);
    }
}
