<?php

use App\Models\User;
use App\Models\Post;

test('create post', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@gmail.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);

    $this->actingAs($admin);

    $response = $this->post('/admin/posts/create', [
        'post' => [
            'title' => 'New Post',
            'text' => 'This is post content',
        ]
    ]);

    $post = Post::query()->where('title', 'New Post')->first();
    expect($post)->not->toBeNull()
        ->and($post->text)->toBe('This is post content');
});

test('post requires title', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    $response = $this->post('/admin/posts/create', [
        'post' => [
            'title' => '',
            'text' => 'Some content',
        ]
    ]);

    $response->assertStatus(500);

    $post = Post::query()->where('text', 'Some content')->first();
    expect($post)->toBeNull();
});

test('post requires text', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    $response = $this->post('/admin/posts/create', [
        'post' => [
            'title' => 'Test Title',
            'text' => '',
        ]
    ]);

    $response->assertStatus(500);

    $post = Post::query()->where('title', 'Test Title')->first();
    expect($post)->toBeNull();
});

test('post has min title length', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    $response = $this->post('/admin/posts/create', [
        'post' => [
            'title' => 'ab',
            'text' => 'Valid content here',
        ]
    ]);

    $response->assertStatus(500);

    $post = Post::query()->where('text', 'Valid content here')->first();
    expect($post)->toBeNull();
});

test('post has max title length', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    $longTitle = str_repeat('a', 101);

    $response = $this->post('/admin/posts/create', [
        'post' => [
            'title' => $longTitle,
            'text' => 'Valid content',
        ]
    ]);

    $response->assertStatus(500);

    $post = Post::query()->where('text', 'Valid content')->first();
    expect($post)->toBeNull();
});

test('post author is automatically assigned', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    $response = $this->post('/admin/posts/create', [
        'post' => [
            'title' => 'Author Test Post',
            'text' => 'This post should have author',
        ]
    ]);

    $post = Post::query()->where('title', 'Author Test Post')->first();

    expect($post)->not->toBeNull()
        ->and($post->author_id)->toBe($admin->id);
});

test('update post', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    $post = Post::query()->create([
        'title' => 'Original Title',
        'text' => 'Original content',
        'author_id' => $admin->id,
    ]);

    $response = $this->post('/admin/posts/update?post=' . $post->id, [
        'post' => [
            'id' => $post->id,
            'title' => 'Updated Title',
            'text' => 'Updated content',
        ]
    ]);

    $updatedPost = Post::query()->find($post->id);
    expect($updatedPost->title)->toBe('Updated Title')
        ->and($updatedPost->text)->toBe('Updated content');
});

test('delete post', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    $post = Post::query()->create([
        'title' => 'Post to Delete',
        'text' => 'This post will be deleted',
        'author_id' => $admin->id,
    ]);

    $postId = $post->id;

    $response = $this->post('/admin/posts/delete?post=' . $postId);

    $deletedPost = Post::query()->find($postId);
    expect($deletedPost)->toBeNull();
});

test('cannot delete non-existent post', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    $response = $this->post('/admin/posts/delete?post=99999');

    $response->assertStatus(302);
});

test('posts are listed for admin', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    Post::query()->create([
        'title' => 'Post 1',
        'text' => 'Content 1',
        'author_id' => $admin->id,
    ]);

    Post::query()->create([
        'title' => 'Post 2',
        'text' => 'Content 2',
        'author_id' => $admin->id,
    ]);

    $response = $this->get('/admin/posts');

    $response->assertStatus(200);

    $posts = Post::all();
    expect($posts)->toHaveCount(2);
});
