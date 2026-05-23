<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequests\PublicationPostRequest;
use App\Http\Requests\PostRequests\GetPostsRequest;
use App\Http\Services\PostService;
use App\Http\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    use ApiResponseTrait;

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
