<?php

namespace App\Http\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostService
{
    public function createPost(array $data): Post
    {
        $post = new Post();
        $post->title = $data['title'];
        $post->text = $data['text'];
        $post->author_id = Auth::user()->id;
        $post->save();
        return $post;
    }

    public function getUserPosts(array $data): array
    {
        $userId = Auth::user()->id;
        $query = Post::query();
        $query->where('author_id', $userId);
        return $this->getFilteredPosts($data, $query);
    }
    public function getPosts(array $data): array
    {
        $query = Post::query();
        return $this->getFilteredPosts($data, $query);
    }

    /**
     * @param array $data
     * @param \Illuminate\Database\Eloquent\Builder|Post $query
     * @return array
     */
    public function getFilteredPosts(array $data, \Illuminate\Database\Eloquent\Builder|Post $query): array
    {
        $limit = $data['limit'] ?? 10;
        $offset = $data['offset'] ?? 0;
        $sortBy = $data['sortBy'] ?? 'date';
        $sortOrder = $data['sortOrder'] ?? 'desc';
        if(!empty($data['dateFrom'])){
            $query->whereDate('created_at', '>=', $data['dateFrom']);
        }
        if(!empty($data['dateTo'])){
            $query->whereDate('created_at', '<=', $data['dateTo']);
        }
        if ($sortBy === 'date') {
            $query->orderBy('created_at', $sortOrder);
        } elseif ($sortBy === 'title') {
            $query->orderBy('title', $sortOrder);
        }
        $total = $query->count();
        $posts = $query->skip($offset)->take($limit)->get();
        return [
            'data' => $posts->map(fn($post) => [
                'id' => $post->id,
                'title' => $post->title,
                'text' => $post->text,
                'author_id' => $post->author_id,
                'created_at' => $post->created_at?->toISOString(),
                'updated_at' => $post->updated_at?->toISOString(),
            ]),
            'meta' => [
                'total' => $total,
                'has_more' => $total > ($offset + $limit),
            ]
        ];
    }
}
