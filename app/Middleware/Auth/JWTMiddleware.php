<?php

declare(strict_types=1);

namespace App\Middleware\Auth;

use App\Helper\JWTHelper;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Container\ContainerInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JWTMiddleware implements MiddlewareInterface
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Hyperf\HttpServer\Contract\RequestInterface
     */
    protected $request;

    /**
     * @var HttpResponse
     */
    protected $response;

    public function __construct(ContainerInterface $container, HttpResponse $response, RequestInterface $request, protected JWTHelper $jwt)
    {
        $this->container = $container;
        $this->response = $response;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $token = JWTHelper::getJWTHeader($request);
            if ($this->jwt->decode($token)) {
                return $handler->handle($request);
            }
        } catch (\Exception $e) {
            return $this->response->json(
                [
                    'code' => $e->getCode(),
                    'data' => [
                        'error' => $e->getMessage(),
                    ],
                ]
            );
        }
        return $handler->handle($request);
    }
}