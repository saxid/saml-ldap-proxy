<?php

namespace Saxid\SaxidLdapProxyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TestAPIController extends Controller
{

    public function ApiAction()
    {
        $w = stream_get_wrappers();
        echo 'openssl: ', extension_loaded('openssl') ? 'yes' : 'no', "</br>\n";
        echo 'http wrapper: ', in_array('http', $w) ? 'yes' : 'no', "</br>\n";
        echo 'https wrapper: ', in_array('https', $w) ? 'yes' : 'no', "</br>\n";
        echo 'wrappers: ', var_export(stream_get_wrappers()) . "</br>";
        echo 'Curl: ', function_exists('curl_version') ? 'Enabled' : 'Disabled';
        print "</br>";

        //$this->curlsfunc();
        $this->callSaxIdApi();

        return $this->render('SaxidLdapProxyBundle::testAPI.html.twig');
    }

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

    function callApi2()
    {
//        error_reporting(E_ALL & E_STRICT);
//        ini_set('display_errors', '5');
//        ini_set('log_errors', '5');
//        ini_set('error_log', './');

        $opts = array('http' =>
            array(
                'method' => 'GET',
                'header' => "Content-Type: application/json\r\n" .
                "Authorization: Token 9fb68218567edb66a7f5ce5e2f916da89b7fc7e5\r\n"
            )
        );

        $context = stream_context_create($opts);

        $result = file_get_contents('https://saxid-api.zih.tu-dresden.de/api/res', false, $context);

        //$result = file_get_contents('https://www.google.de');

        var_dump($result);
    }

    public function callSaxIdApi()
    {


        $apiurl = "https://saxid-api.zih.tu-dresden.de/api/res/";
        $apitoken = "9fb68218567edb66a7f5ce5e2f916da89b7fc7e5";
        $username = "myuser2@tu-dresden.de";
        $userid = "myuser2@tu-dresden.de";
        $format = 'Y-m-d\TH:i:s\Z';
        $sp = "076f2d546d034c8f923c9bb76aa37c9e";
        $setuptoken = "1234";
        $header = sprintf("Content-Type: application/json\r\nAuthorization: Token %s\r\n", $apitoken);

        $data = [
            'eppn' => $username,
            
            'expiry_date' => date($format, mktime(0, 0, 0, date('m'), date('d') + 7)),
            'deletion_date' => date($format, mktime(0, 0, 0, date('m'), date('d') + 7 + 30)),
            'sp' => $sp,
            'setup_token' => $setuptoken
        ];
        $options = [
            'http' => [
                'header' => $header,
                'method' => 'POST',
                'content' => json_encode($data),
            ],
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($apiurl, false, $context);

        var_dump(json_decode($result));
    }
}