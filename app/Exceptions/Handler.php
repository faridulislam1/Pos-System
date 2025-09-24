<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }


    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'success' => false,
                'statusCode' => 401,
                'message' => 'Authorization token not found'
            ], 401);
        }
        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'status' => 404,
                'message' => 'Route not found. Please check URL.'
            ], 404);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'status' => 405,
                'message' => 'HTTP method not allowed for this route.'
            ], 405);
        }

        if ($exception instanceof ThrottleRequestsException) {
            return response()->json([
                'error' => $exception->getMessage() ?: 'Too many requests. Please try again later.'
            ], 429);
        }

        return parent::render($request, $exception);
    }
}
