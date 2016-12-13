<?php

namespace Saxid\SaxidLdapProxyBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LdapControllerTest extends WebTestCase
{
    public function testAdduser()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/user');

        $this->assertTrue($crawler->filter('html:contains("Benutzerdaten für")')->count() > 0);
    }

}
