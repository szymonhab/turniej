<?php

namespace Szymon\TurniejBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TurniejControllerTest extends WebTestCase
{
    public function testNowyturniej()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/nowy_turniej');
    }

}
