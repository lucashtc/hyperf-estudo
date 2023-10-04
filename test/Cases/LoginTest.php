<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace HyperfTest\Cases;

use Hyperf\Testing\Client;
use HyperfTest\HttpTestCase;

/**
 * @internal
 * @coversNothing
 */
class LoginTest extends HttpTestCase
{
    public function testLoginValidToken()
    {
        $client = make(Client::class);
        $response = $client->post("/login", ['email' => "Lucas", "password" => "Lucas"]);

        $this->assertNotEmpty($response);
        $this->assertIsString($response['data']['token']);
        $this->assertEquals(200, $response['code']);
    }

    public function testUserNotFound()
    {
        $client = make(Client::class);
        $response = $client->post("/login", ['email' => "L", "password" => "L"]);

        $this->assertNotEmpty($response);
        $this->assertEquals("User Not Found", $response['data']['error']);
        $this->assertEquals(401, $response['code']);
    }
}