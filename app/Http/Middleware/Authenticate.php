<?php

namespace App\Http\Middleware;

use App\Helpers\ErrorCodes;
use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Response;

/**
 * Class Authenticate
 *
 * @package App\Http\Middleware
 */
class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var Auth
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @param null $guard
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth->guard($guard)->guest()) {
            $response = [
                'responseType' => Controller::RESPONSE_ERROR,
                'data' => null,
                'errorMessage' => 'Unauthorized',
                'errorCode' => ErrorCodes::LOGIN_REQUIRED
            ];

            $statusCode = Response::HTTP_UNAUTHORIZED;

            return response()->json($response, $statusCode);
        }

        return $next($request);
    }
}
