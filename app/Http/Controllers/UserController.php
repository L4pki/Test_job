<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequests\AuthorizationUserRequest;
use App\Http\Requests\UserRequests\RegistrationUserRequest;
use App\Http\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\ApiResponseTrait;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "User",
    type: "object",
    required: ["name", "email", "password"],
    properties: [
        new OA\Property(
            property: "name",
            type: "string",
            description: "User name"
        ),
        new OA\Property(
            property: "email",
            type: "string",
            format: "email",
            description: "User email"
        ),
        new OA\Property(
            property: "password",
            type: "string",
            description: "User password"
        )
    ]
)]
class UserController extends Controller
{
    use ApiResponseTrait;
    #[OA\Post(
        path: "/api/user/create",
        summary: "Create new user",
        tags: ["Users"],
        requestBody: new OA\RequestBody(
            description: "User login credentials",
            required: true,
            content: new OA\JsonContent(
                required: ["name","email", "password"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "john"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful operation",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/User")
                )
            )
        ]
    )]
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
    #[OA\Post(
        path: "/api/user/login",
        summary: "Authorization user",
        tags: ["Users"],
        requestBody: new OA\RequestBody(
            description: "User login credentials",
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful operation",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/User")
                )
            )
        ]
    )]
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
    #[OA\Post(
        path: "/api/user/refresh",
        summary: "Update token successfully",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Tokens refreshed successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "accessToken",
                            type: "string",
                            description: "New JWT access token (valid for 1 day)"
                        ),
                        new OA\Property(
                            property: "refreshToken",
                            type: "string",
                            description: "New JWT refresh token (valid for 1 week)"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Invalid or expired token")
                    ]
                )
            )
        ]
    )]
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
