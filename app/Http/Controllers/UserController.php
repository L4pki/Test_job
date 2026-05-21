<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequests\RegistrationUserRequest;
use App\Http\Services\UserService;

class UserController extends Controller
{
    public function registrationUser(RegistrationUserRequest $request): array
    {
        $userService = new UserService();
        return $userService->createUser($request->validated());
    }

    public function authorizationUser(authorizationUserRequest $request): array
    {
        return [];
    }
}
