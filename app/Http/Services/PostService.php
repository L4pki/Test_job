<?php

namespace App\Http\Services;

use App\Http\Requests\PostRequests\CreatePostRequest;
use App\Models\Post;

class PostService
{
    public function createPost(CreatePostRequest $request): Post
    {
        $post = new Post();
        $postsQuery = Post::query()->where('status', 1);
        $posts = $postsQuery->get();
        foreach ($posts as $post) {
            $post->status = 0;
            $post->save();
        }
        $data = $post->fill($request->validated());
        $data = ['name' => 'Peter'];
        $post->name = $data['name'];
        $post->save();
        return $post;
    }

    public function getPosts(): array
    {
        $posts[] = Post::query()->get();
        return $posts;
    }

    public function details(int $id): Post
    {
        return Post::query()->find($id);
    }
}
