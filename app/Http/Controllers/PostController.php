<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequests\CreatePostRequest;
use App\Http\Requests\PostRequests\GetPostsRequest;
use App\Http\Services\PostService;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    public function createPost(CreatePostRequest $request): array
    {
        /*
        $service = new PostService();
        $post = $service->createPost($request->validated());
        return [
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
            ]
        ];*/
        return[];
    }

    public function getPosts(GetPostsRequest $request): JsonResponse
    {
        $postService = new PostService();
        $posts = $postService->getPosts();
        return response()->json(['posts' => $posts]);
    }

    public function getUserPosts(GetUserPostsRequest $request): JsonResponse
    {
        $authorId = $request->validated()['author_Id'];
        $posts = Post::query()->where('author_id', $authorId)->get();
        return response()->json(['posts' => $posts]);
    }
}
