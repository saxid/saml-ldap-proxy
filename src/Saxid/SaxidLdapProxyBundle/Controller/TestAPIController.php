<?php

namespace Saxid\SaxidLdapProxyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use \Saxid\SaxidLdapProxyBundle\Services\SaxIDAPI;

class TestAPIController extends Controller
{

    public function ApiAction()
    {
        $SaxIDApiAccess = new SaxIDAPI("https://saxid-api.zih.tu-dresden.de/api/", "9fb68218567edb66a7f5ce5e2f916da89b7fc7e5");

        $format = 'Y-m-d\TH:i:s\Z';
        $expiryDate = date($format, mktime(0, 0, 0, date('m'), date('d') + 365));
        $deletionDate = date($format, mktime(0, 0, 0, date('m'), date('d') + 365 + 30));

        //$SaxIDApiAccess->createAPIEntry("TUDUSER@tu-dresden.de", "076f2d546d034c8f923c9bb76aa37c9e", $deletionDate, $expiryDate);
        $SaxIDApiAccess->getServices();
        return $this->render('SaxidLdapProxyBundle::testAPI.html.twig');
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
            var_dump(curl_error($ch));
            //trigger_error(curl_error($ch));
        }

        curl_close($ch);

        $myVar = json_decode($result);
        print "Result: </br>";
        var_dump($myVar);
    }
}