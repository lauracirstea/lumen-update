<?php

namespace App\Providers;

use App\Helpers\JWT;
use App\User;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->header('Authorization')) {
                $requestToken = explode(' ', $request->header('Authorization'));

                if (isset($requestToken[1])) {
                    try {
                        $userPayload = JWT::validateToken($requestToken[1]);

                        return \App\Models\User::where('id', $userPayload['id'])->first();
                    } catch (\Exception $e) {
                        return null;
                    }
                }

                return null;
            }

            return null;
        });
    }
}
