<?php

namespace Szymon\TurniejBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RozgrywkaControllerTest extends WebTestCase
{
    public function testRozpocznij()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/rozpocznij');
    }

}
