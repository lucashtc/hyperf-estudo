<?php

declare(strict_types=1);

namespace App\Service\Auth;

use App\Model\User;

class LoginService
{
    public function findUser(string $email, string $password): \Hyperf\Database\Model\Collection
    {
        return User::where("email", $email)->where("password", $password)->get();
    }
}