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
use Hyperf\HttpServer\Router\Router;
use App\Middleware\Auth\JWTMiddleware;
use App\Controller\AuthController;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index', ['middleware' => [JWTMiddleware::class]]);

Router::post('/login', [AuthController::class, 'login']);

Router::get('/favicon.ico', function () {
    return '';
});