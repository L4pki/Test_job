<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequests\PublicationPostRequest;
use App\Http\Requests\PostRequests\GetPostsRequest;
use App\Http\Services\PostService;
use App\Http\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Post",
    type: "object",
    required: ["title", "text"],
    properties: [
        new OA\Property(
            property: "title",
            type: "string",
            description: "Title"
        ),
        new OA\Property(
            property: "text",
            type: "string",
            description: "Text"
        )
    ]
)]
class PostController extends Controller
{
    use ApiResponseTrait;

    #[OA\Post(
        path: "/api/post/create",
        summary: "Create new post",
        tags: ["Posts"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title","text"],
                properties: [
                    new OA\Property(property: "title", type: "string", example: "title"),
                    new OA\Property(property: "text", type: "string", example: "text for example")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful operation",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/Post")
                )
            )
        ]
    )]
    public function publicationPost(PublicationPostRequest $request): JsonResponse
    {
        try {
            $service = new PostService();
            $result = $service->createPost($request->validated());
            return $this->successResponse(
                $result,
                'Post published successfully',
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                400
            );
        }
    }

    #[OA\Get(
        path: "/api/post/get/all",
        summary: "Get all posts with pagination and filters",
        tags: ["Posts"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "limit",
                in: "query",
                description: "Items per page",
                required: false,
                schema: new OA\Schema(type: "integer", minimum: 1, maximum: 100)
            ),
            new OA\Parameter(
                name: "offset",
                in: "query",
                description: "Number of items to skip",
                required: false,
                schema: new OA\Schema(type: "integer", minimum: 0)
            ),
            new OA\Parameter(
                name: "sortBy",
                in: "query",
                description: "Field to sort by",
                required: false,
                schema: new OA\Schema(type: "string", enum: ["date", "title"])
            ),
            new OA\Parameter(
                name: "sortOrder",
                in: "query",
                description: "Sort order",
                required: false,
                schema: new OA\Schema(type: "string", enum: ["asc", "desc"])
            ),
            new OA\Parameter(
                name: "dateFrom",
                in: "query",
                description: "Filter posts from date",
                required: false,
                schema: new OA\Schema(type: "string", format: "date", example: "2024-01-01")
            ),
            new OA\Parameter(
                name: "dateTo",
                in: "query",
                description: "Filter posts to date",
                required: false,
                schema: new OA\Schema(type: "string", format: "date", example: "2024-12-31")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful operation",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/Post")
                        ),
                        new OA\Property(
                            property: "meta",
                            properties: [
                                new OA\Property(property: "total", type: "integer"),
                                new OA\Property(property: "limit", type: "integer"),
                                new OA\Property(property: "offset", type: "integer"),
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function getPosts(GetPostsRequest $request): JsonResponse
    {
        try {
            $postService = new PostService();
            $result = $postService->getPosts($request->validated());
            return $this->successResponse(
                $result,
                'Get posts successfully',
                200
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                400
            );
        }
    }

    #[OA\Get(
        path: "/api/post/get/user",
        summary: "Get user posts with pagination and filters",
        tags: ["Posts"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "limit",
                in: "query",
                description: "Items per page",
                required: false,
                schema: new OA\Schema(type: "integer", minimum: 1, maximum: 100)
            ),
            new OA\Parameter(
                name: "offset",
                in: "query",
                description: "Number of items to skip",
                required: false,
                schema: new OA\Schema(type: "integer", minimum: 0)
            ),
            new OA\Parameter(
                name: "sortBy",
                in: "query",
                description: "Field to sort by",
                required: false,
                schema: new OA\Schema(type: "string", enum: ["date", "title"])
            ),
            new OA\Parameter(
                name: "sortOrder",
                in: "query",
                description: "Sort order",
                required: false,
                schema: new OA\Schema(type: "string", enum: ["asc", "desc"])
            ),
            new OA\Parameter(
                name: "dateFrom",
                in: "query",
                description: "Filter posts from date",
                required: false,
                schema: new OA\Schema(type: "string", format: "date", example: "2024-01-01")
            ),
            new OA\Parameter(
                name: "dateTo",
                in: "query",
                description: "Filter posts to date",
                required: false,
                schema: new OA\Schema(type: "string", format: "date", example: "2024-12-31")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful operation",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/Post")
                        ),
                        new OA\Property(
                            property: "meta",
                            properties: [
                                new OA\Property(property: "total", type: "integer"),
                                new OA\Property(property: "limit", type: "integer"),
                                new OA\Property(property: "offset", type: "integer"),
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function getUserPosts(GetPostsRequest $request): JsonResponse
    {
        try {
            $postService = new PostService();
            $result = $postService->getUserPosts($request->validated());
            return $this->successResponse(
                $result,
                'Get user posts successfully',
                200
            );
        } catch (Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                400
            );
        }
    }
}
