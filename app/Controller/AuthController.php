<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\JWTHelper;
use App\Request\Auth\LoginRequest;
use App\Service\Auth\LoginService;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends AbstractController
{
    public function login(LoginRequest $request, ResponseInterface $response, LoginService $loginService)
    {

        $jwt = new JWTHelper();
        $validated = $request->validated();
        var_dump($validated);
        $email = $validated["email"];
        $password = $validated["password"];


        if ($loginService->findUser($email, $password)->count() !== 0) {
            return $response->json([
                'user' => 'test',
                'code' => Response::HTTP_OK,
                'data' => [
                    'token' => $jwt->encode(),
                    'type' => "Bearer",
                ],
            ]);
        }

        return $response->json([
            'code' => Response::HTTP_UNAUTHORIZED,
            'data' => [
                'error' => "User Not Found"
            ],
        ]);

    }
}