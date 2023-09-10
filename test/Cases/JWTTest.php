<?php

declare(strict_types=1);

namespace HyperfTest\Cases;

use HyperfTest\HttpTestCase;
use App\Service\JWT\JWTAuth;

/**
 * @internal
 * @coversNothing
 */
class JWTTest extends HttpTestCase
{
    public function testValidEncodeDecode()
    {
        $jwt = new JWTAuth();

        $jwt->setPayload("rule", "ADMIN");
        $encoded = $jwt->encode();
        $decoded = $jwt->decode($encoded);

        $this->assertEquals($decoded->rule, "ADMIN");
    }

    public function testValidDecodeExpired()
    {
        $jwt = new JWTAuth();

        $dtExpired = new \DateTimeImmutable();
        $dtExpired->modify("- 1 second")->getTimestamp();
        $jwt->setPayload("exp", $dtExpired);

        $encoded = $jwt->encode();
        $decoded = $jwt->decode($encoded);

        $this->assertNull($decoded);
    }

    public function testInvalidEncodeDecode()
    {
        $jwt = new JWTAuth();

        $decode = $jwt->decode("23423fwedfwef");
        $this->assertNull($decode);
    }

    public function testValidRefresh()
    {
        $jwt = new JWTAuth();

        $encoded = $jwt->encode();
        $jwtRefresh = $jwt->refresh($encoded);

        $this->assertIsString($jwtRefresh);
    }
}