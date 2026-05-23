<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequests\AuthorizationUserRequest;
use App\Http\Requests\UserRequests\RegistrationUserRequest;
use App\Http\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\ApiResponseTrait;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function registrationUser(RegistrationUserRequest $request): JsonResponse
    {
        try {
            $userService = new UserService();
            $result = $userService->createUser($request->validated());
            return $this->successResponse(
                $result,
                'User registered successfully',
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                400
            );
        }

    }

    /**
     * @throws Exception
     */
    public function authorizationUser(AuthorizationUserRequest $request): JsonResponse
    {
        try {
            $userService = new UserService();
            $result = $userService->loginUser($request->validated());

            return $this->successResponse(
                $result,
                'Login successful',
                200
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                401
            );
        }
    }

    /**
     * @throws Exception
     */
    public function refreshToken(): JsonResponse
    {
        try {
            $userService = new UserService();
            $result = $userService->refreshTokenUser();

            return $this->successResponse(
                $result,
                'Tokens refreshed successfully',
                200
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                401
            );
        }
    }
}
