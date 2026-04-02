<?php

namespace App\Tests\routes;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class authTest extends WebTestCase
{
    #[DataProvider('routeProviderAuth')]
    public function testRouteAuth(string $uri)
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, $uri);
        $this->assertResponseStatusCodeSame(302);

    }

    public static function routeProviderAuth(): Generator
    {

        yield ["/account/val"];
        yield ["/settings/profile"];
        yield ["/settings/"];
    }
}
