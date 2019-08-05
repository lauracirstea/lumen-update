<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class UserService
 *
 * @package App\Services
 */
class UserService
{
    /**
     * Validate request on login
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validateLoginRequest(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        $messages = [
            'email.required' => 'errors.email.required',
            'email.email' => 'errors.email.invalid',
            'password.required' => 'errors.password.required'
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    public function validateCreateRequest(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required'
        ];

        $messages = [
            'name.required' => 'errors.name.required',
            'email.required' => 'errors.email.required',
            'email.email' => 'errors.email.invalid',
            'password.required' => 'errors.password.required'
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * Validate request on login with remember token
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validateTokenLoginRequest(Request $request)
    {
        $rules = [
            'rememberToken' => 'required'
        ];

        $messages = [
            'rememberToken.required' => 'errors.rememberToken.required'
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    public function validateForgotPassword(Request $request)
    {
        $rules = [
            'email' => 'required|email'
        ];

        $messages = [
            'email.required' => 'errors.email.required'
        ];

        return Validator::make($request->all(), $rules, $messages);
    }
    public function validateChangePassword(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'forgot_code' => 'required'
        ];

        $messages = [
            'email.required' => 'errors.email.required',
            'forgot_code.required' => 'errors.forgot_code.required'
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * Login with token user
     *
     * @param $token
     *
     * @return User|null
     */
    public function loginUserWithRememberToken($token)
    {
        return User::whereHas('userTokens', function ($query) use ($token) {
            $query->where('token', $token)
                ->where('expire_at', '>=', Carbon::now()->format('Y-m-d H:i:s'));
        })->first();
    }

    /**
     * Update remember token valability when used on login
     *
     * @param $token
     */
    public function updateRememberTokenValability($token)
    {
        $userToken = UserToken::where('token', $token)
            ->where('type', UserToken::TYPE_REMEMBER)
            ->first();

        if ($userToken) {
            $userToken->expire_at = Carbon::now()->addDays(14)->format('Y-m-d H:i:s');

            $userToken->save();
        }
    }

    /**
     * Login user
     *
     * @param array $credentials
     *
     * @return User|null
     */
    public function loginUser(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return null;
        }

        $password = $user->password;

        if (app('hash')->check($credentials['password'], $password)) {
            return $user;
        }

        return null;
    }

    /**
     * Generate remember me token
     *
     * @param $userId
     *
     * @return string
     */
    public function generateRememberMeToken($userId)
    {
        $userToken = new UserToken();

        $userToken->user_id = $userId;
        $userToken->token = str_random(64);
        $userToken->type = UserToken::TYPE_REMEMBER;
        $userToken->expire_at = Carbon::now()->addDays(14)->format('Y-m-d H:i:s');

        $userToken->save();

        return $userToken->token;
    }
}
