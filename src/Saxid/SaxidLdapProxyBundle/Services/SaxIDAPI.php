<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Saxid\SaxidLdapProxyBundle\Services;

/**
 * Description of SaxIDAPI
 *
 * @author Norman
 */
class SaxIDAPI
{
    //Variables
    private $apiBaseURL;
    private $authToken;
    private $setupToken;

    /**
     * Creates SaxIDAPI access object
     *
     * @param string $apiBaseURL the base url e.g. 'https://saxid-api.zih.tu-dresden.de/api/'
     * @param string $authToken auth token to identify the request
     * @param string $setupToken not used yet

     */
    function __construct($apiBaseURL, $authToken, $setupToken = "1234")
    {
        $this->apiBaseURL = $apiBaseURL;
        $this->authToken = $authToken;
        $this->setupToken = $setupToken;
    }

     /**
     * GET: Liste aller Service Provider (Dienste), die anderen Einrichtungen zur Verfügung stehen
     */
    public function getServices()
    {
        $apiurl = $this->apiBaseURL . "services/";
        $header = sprintf("Authorization: Token %s\r\n", $this->authToken);

        $options = [
            'http' => [
                'method' => 'GET',
                'header' => $header
            ],
        ];

        // execute request
        $result = file_get_contents($apiurl, false, stream_context_create($options));
    }

     /**
     * GET: Liste aller Ressourcen und deren Attribute, die Nutzer der anfragenden Einrichtung lokal besitzen
     */
    public function getRessources()
    {
        $apiurl = $this->apiBaseURL . "res/";
        $header = sprintf("Authorization: Token %s\r\n", $this->authToken);

        $options = [
            'http' => [
                'method' => 'GET',
                'header' => $header
            ],
        ];

        // execute request
        $result = file_get_contents($apiurl, false, stream_context_create($options));
    }

    /**
     * POST: Erstellt lokal neue Ressource anhand eines SetupToken und Metadaten, die durch das Plugin eines Dienstes mitgesendet werden
     *
     * @param string $eppn EduPersonPrinicalName e.g. user1@tu-dresden.de
     * @param string $spUUID uuid for the sp in the api
     * @param string $deleteDate delete Date
     * @param string $expirationDate expiry date
     *
     */
    public function createAPIEntry($eppn, $spUUID = '076f2d546d034c8f923c9bb76aa37c9e', $deleteDate, $expirationDate)
    {
        $apiurl = $this->apiBaseURL . "res/";
        //$format = 'Y-m-d\TH:i:s\Z';
        $header = sprintf("Content-Type: application/json\r\nAuthorization: Token %s\r\n", $this->authToken);

        // data for post request
        $data = [
            'eppn' => $eppn,
            'expiry_date' => $expirationDate,
            'deletion_date' => $deleteDate,
            'sp' => $spUUID,
            'setup_token' => $this->setupToken
        ];
        $options = [
            'http' => [
                'header' => $header,
                'method' => 'POST',
                'content' => json_encode($data),
            ],
        ];

        // execute request
        $result = file_get_contents($apiurl, false, stream_context_create($options));
    }
}
