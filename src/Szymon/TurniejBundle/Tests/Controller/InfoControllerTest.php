<?php

namespace Szymon\TurniejBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InfoControllerTest extends WebTestCase
{
    public function testInstrukcje()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/instrukcje');
    }

    public function testPrzygotowanie()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/przygotowanie');
    }

    public function testOprogramie()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/oProgramie');
    }

}
