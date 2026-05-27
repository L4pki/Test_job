<?php

use App\Models\User;

test('create user', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);

    $this->actingAs($admin);

    $response = $this->post('/admin/users/create', [
        'user' => [
            'name' => 'New user',
            'email' => 'user@gmail.com',
            'password' => '123456',
        ]
    ]);

    $user = User::query()->where('email', 'user@gmail.com')->first();
    expect($user)->not->toBeNull();
});

test('duplicate email shows error', function () {
    $admin = User::query()->create([
        'name' => 'Existing',
        'email' => 'exists@gmail.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    $response = $this->post('/admin/users/create', [
        'user' => [
            'name' => 'New',
            'email' => 'exists@gmail.com',
            'password' => '123456',
        ]
    ]);
    $response->assertStatus(302);
    $users = User::query()->where('email', 'exists@gmail.com')->get();
    expect($users)->toHaveCount(1);
});

test('user requires name', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    $response = $this->post('/admin/users/create', [
        'user' => [
            'name' => '',
            'email' => 'noname@gmail.com',
            'password' => '123456',
        ]
    ]);

    $response->assertStatus(500);

    $user = User::query()->where('email', 'noname@gmail.com')->first();
    expect($user)->toBeNull();
});

test('user requires email', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    $response = $this->post('/admin/users/create', [
        'user' => [
            'name' => 'No Email User',
            'email' => '',
            'password' => '123456',
        ]
    ]);

    $response->assertStatus(500);

    $user = User::query()->where('name', 'No Email User')->first();
    expect($user)->toBeNull();
});

test('user requires password', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    $response = $this->post('/admin/users/create', [
        'user' => [
            'name' => 'No Password User',
            'email' => 'nopass@gmail.com',
            'password' => '',
        ]
    ]);

    $response->assertStatus(500);

    // Пользователь не должен создаться
    $user = User::query()->where('email', 'nopass@gmail.com')->first();
    expect($user)->toBeNull();
});

test('user has min name length', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    $response = $this->post('/admin/users/create', [
        'user' => [
            'name' => 'ab',
            'email' => 'shortname@gmail.com',
            'password' => '123456',
        ]
    ]);

    $response->assertStatus(500);

    $user = User::query()->where('email', 'shortname@gmail.com')->first();
    expect($user)->toBeNull();
});

test('user has max name length', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    $longName = str_repeat('a', 101);

    $response = $this->post('/admin/users/create', [
        'user' => [
            'name' => $longName,
            'email' => 'longname@gmail.com',
            'password' => '123456',
        ]
    ]);

    $response->assertStatus(500);

    $user = User::query()->where('email', 'longname@gmail.com')->first();
    expect($user)->toBeNull();
});

test('user has min password length', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    $response = $this->post('/admin/users/create', [
        'user' => [
            'name' => 'Valid Name',
            'email' => 'shortpass@gmail.com',
            'password' => '123',
        ]
    ]);

    $response->assertStatus(500);

    $user = User::query()->where('email', 'shortpass@gmail.com')->first();
    expect($user)->toBeNull();
});

test('user has max password length', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    $longPassword = str_repeat('a', 21);

    $response = $this->post('/admin/users/create', [
        'user' => [
            'name' => 'Valid Name',
            'email' => 'longpass@gmail.com',
            'password' => $longPassword,
        ]
    ]);

    $response->assertStatus(500);

    $user = User::query()->where('email', 'longpass@gmail.com')->first();
    expect($user)->toBeNull();
});

test('user requires valid email format', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    $response = $this->post('/admin/users/create', [
        'user' => [
            'name' => 'Valid Name',
            'email' => 'not-an-email',
            'password' => '123456',
        ]
    ]);

    $response->assertStatus(500);

    $user = User::query()->where('name', 'Valid Name')->first();
    expect($user)->toBeNull();
});

test('create user with admin permissions', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    $response = $this->post('/admin/users/create', [
        'user' => [
            'name' => 'New Admin',
            'email' => 'newadmin@gmail.com',
            'password' => '123456',
            'is_admin' => true,
        ]
    ]);

    $user = User::query()->where('email', 'newadmin@gmail.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->permissions)->not->toBeNull()
        ->and($user->permissions)->toBeArray()
        ->and($user->permissions)->toHaveKey('platform.index');
});

test('update user', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    $user = User::query()->create([
        'name' => 'Original Name',
        'email' => 'update@test.com',
        'password' => bcrypt('123456'),
    ]);

    $response = $this->post('/admin/users/update?user=' . $user->id, [
        'user' => [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@test.com',
        ]
    ]);

    $updatedUser = User::query()->find($user->id);
    expect($updatedUser->name)->toBe('Updated Name')
        ->and($updatedUser->email)->toBe('updated@test.com');
});

test('update user password', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    $user = User::query()->create([
        'name' => 'Password User',
        'email' => 'password@test.com',
        'password' => bcrypt('oldpassword'),
    ]);

    $oldPassword = $user->password;

    $response = $this->post('/admin/users/update?user=' . $user->id, [
        'user' => [
            'id' => $user->id,
            'name' => 'Password User',
            'email' => 'password@test.com',
            'password' => 'newpassword123',
        ]
    ]);

    $updatedUser = User::query()->find($user->id);
    expect($updatedUser->password)->not->toBe($oldPassword);
});

test('delete user', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    $user = User::query()->create([
        'name' => 'User to Delete',
        'email' => 'delete@test.com',
        'password' => bcrypt('123456'),
    ]);

    $userId = $user->id;

    $response = $this->post('/admin/users/delete?user=' . $userId);

    $deletedUser = User::query()->find($userId);
    expect($deletedUser)->toBeNull();
});

test('cannot delete non-existent user', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    $response = $this->post('/admin/users/delete?user=99999');

    $response->assertStatus(302);
});

test('users are listed for admin', function () {
    $admin = User::query()->create([
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => bcrypt('123456'),
        'permissions' => ['platform.index' => true],
    ]);
    $this->actingAs($admin);

    User::query()->create([
        'name' => 'User 1',
        'email' => 'user1@test.com',
        'password' => bcrypt('123456'),
    ]);

    User::query()->create([
        'name' => 'User 2',
        'email' => 'user2@test.com',
        'password' => bcrypt('123456'),
    ]);

    $response = $this->get('/admin/users');

    $response->assertStatus(200);

    $users = User::all();
    expect($users)->toHaveCount(3);
});
