<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Repositories\UserRepository;
use Illuminate\Support\Facades\Config;

class AuthController extends Controller
{
    private $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function loginWithGoogle(Request $request)
    {
        $data = Socialite::driver('google')->userFromToken($request->input('token'));
        $user = $this->users->getUserByEmail($data->email);
        if (empty($user)) {
            $user = $this->users->createGoogleUser($data->user);
        }

        return $this->LoginWithSocial($user);
    }

    private function LoginWithSocial($user)
    {
        if (!$token = JWTAuth::fromUser($user)) {
            return response()->json(['message' => Config::get('global.message.login_fail')], 422);
        }
        auth()->setUser($user);

        return $this->respondWithToken($token);
    }

    public function me()
    {
        $user = auth()->user();
        if (empty($user)) {
            return response()->json(['message' => Config::get('global.message.unauthorized')]);
        }
        $user->name = "{$user->firstname} {$user->lastname}";
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();
        return response()->json([
            'message' => Config::get('global.message.login_success')
        ]);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
