<?php

namespace App\Tests\routes;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PublicTest extends WebTestCase
{
    #[DataProvider('routeProviderPublic')]
    public function testRoutePublic(string $uri)
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, $uri);
        $this->assertResponseStatusCodeSame(200);
    }

    public static function routeProviderPublic(): Generator
    {

        yield ["/"];
    }
}
