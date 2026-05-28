<?php

namespace App\Http\Services;

use App\Models\PersonalAccessToken;
use App\Models\TokenConnection;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * @throws Exception
     */
    public function createUser(array $data): array
    {
        if(User::query()->where('email', $data['email'])->exists()){
            throw new Exception('User already exists');
        }
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->save();
        return $this->tokenUpdate($user);
    }

    /**
     * @throws Exception
     */
    public function loginUser(array $data): array
    {
        $email = $data['email'];
        $user = User::query()->where('email', $email)->first();
        if(!$user){
            throw new Exception('User not found');
        }
        if(!Hash::check($data['password'], $user->password)){
            throw new Exception('Wrong password');
        }
        $user->save();
        return $this->tokenUpdate($user);
    }

    /**
     * @throws Exception
     */
    public function refreshTokenUser(): array
    {
        $currentToken = Auth::user()->currentAccessToken();
        $tokenConnection = TokenConnection::query()
            ->where('refresh_token_id', $currentToken->id)->first();
        $personalAccessToken = PersonalAccessToken::query()
            ->where('id', $tokenConnection->access_token_id)->first();
        $personalRefreshToken = PersonalAccessToken::query()
            ->where('id', $currentToken->id)->first();
        $tokenConnection->delete();
        $personalAccessToken->delete();
        $personalRefreshToken->delete();
        $user = Auth::user();

        return $this->tokenUpdate($user);
    }

    /**
     * @param User $user
     * @return array
     */
    public function tokenUpdate(User $user): array
    {
        $tokenConnection = new TokenConnection();
        $accessToken = $user->createToken('access_token', ['*'], now()->addDay());
        $refreshToken = $user->createToken('refresh_token', ['*'], now()->addWeek());
        $tokenConnection->refresh_token_id = $refreshToken->accessToken->id;
        $tokenConnection->access_token_id = $accessToken->accessToken->id;
        $tokenConnection->save();
        return ['accessToken' => $accessToken->plainTextToken, 'refreshToken' => $refreshToken->plainTextToken];
    }
}
