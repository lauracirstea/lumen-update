<?php

namespace App\Exceptions;

use App\Helpers\ErrorCodes;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Exception $exception
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Exception $exception
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function render($request, Exception $exception)
    {
        if (env('APP_DEBUG')) {
            return parent::render($request, $exception);
        }

        $response = [
            'responseType' => Controller::RESPONSE_ERROR,
            'data' => null,
            'errorMessage' => 'Something went wrong!',
            'errorCode' => ErrorCodes::FRAMEWORK_ERROR,
        ];

        $statusCode = Response::HTTP_OK;

        return response()->json($response, $statusCode);
    }
}
