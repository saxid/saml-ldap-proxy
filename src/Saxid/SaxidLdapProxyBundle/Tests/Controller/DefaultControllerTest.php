<?php

namespace Saxid\SaxidLdapProxyBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertTrue($crawler->filter('html:contains("Folgende Attributswerte wurden vom Identity Provider übermittelt")')->count() > 0);
    }
}
