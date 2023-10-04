<?php

declare(strict_types=1);

namespace HyperfTest\Cases;

use App\Exception\Handler\AuthJWTException;
use App\Helper\JWTHelper;
use Hyperf\Context\Context;
use Hyperf\HttpServer\Request;
use HyperfTest\HttpTestCase;
use Mockery;
use Psr\Http\Message\ServerRequestInterface;

class HelperJWTTest extends HttpTestCase
{
    public function testValidHeader()
    {
        $wantJWT = '123456789';

        $psrRequest = Mockery::mock(ServerRequestInterface::class);
        $psrRequest->shouldReceive('getHeader')->with('authorization')->andReturn(['Bearer ' . $wantJWT]);
        Context::set(ServerRequestInterface::class, $psrRequest);

        $mockRequest = new Request();

        $gotJWt = JWTHelper::getJWTHeader($mockRequest);
        $this->assertEquals($gotJWt, $wantJWT);
    }

    public function testTokenNotFoundHeader()
    {
        $psrRequest = Mockery::mock(ServerRequestInterface::class);
        $psrRequest->shouldReceive('getHeader')->with('authorization')->andReturn(['123654']);
        Context::set(ServerRequestInterface::class, $psrRequest);

        $mockRequest = new Request();

        $this->expectException(AuthJWTException::class);
        $this->expectExceptionMessage("token not found");

        JWTHelper::getJWTHeader($mockRequest);
    }

    public function testHeaderNotFound()
    {
        $psrRequest = Mockery::mock(ServerRequestInterface::class);
        $psrRequest->shouldReceive('getHeader')->with('authorization')->andReturn([]);
        Context::set(ServerRequestInterface::class, $psrRequest);

        $mockRequest = new Request();

        $this->expectException(AuthJWTException::class);
        $this->expectExceptionMessage("authorization header not found");

        JWTHelper::getJWTHeader($mockRequest);
    }

    public function testValidEncodeDecode()
    {
        $jwt = new JWTHelper();

        $jwt->setPayload("rule", "ADMIN");
        $encoded = $jwt->encode();
        $decoded = $jwt->decode($encoded);

        $this->assertEquals($decoded->rule, "ADMIN");
    }

    public function testValidDecodeExpired()
    {
        $this->expectException(AuthJWTException::class);
        $jwt = new JWTHelper();

        $dtExpired = new \DateTimeImmutable();
        $dtExpired->modify("- 1 second")->getTimestamp();
        $jwt->setPayload("exp", $dtExpired);

        $encoded = $jwt->encode();
        $decoded = $jwt->decode($encoded);

    }

    public function testInvalidEncodeDecode()
    {
        $this->expectException(AuthJWTException::class);
        $jwt = new JWTHelper();

        $decode = $jwt->decode("23423fwedfwef");
    }

    public function testValidRefresh()
    {
        $jwt = new JWTHelper();

        $encoded = $jwt->encode();
        $jwtRefresh = $jwt->refresh($encoded);

        $this->assertIsString($jwtRefresh);
    }
}