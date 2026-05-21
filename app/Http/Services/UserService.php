<?php

namespace App\Http\Services;

use App\Models\User;

class UserService
{
    public function createUser(array $data): array
    {
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->is_admin = 0;
        $user->save();
        $accessToken = $user->createToken('access_token')->plainTextToken;
        $refreshToken = $user->createToken('refresh_token')->plainTextToken;
        return ['accessToken' => $accessToken, 'refreshToken' =>$refreshToken];
    }
}
