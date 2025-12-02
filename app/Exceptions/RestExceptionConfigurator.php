<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class RestExceptionConfigurator
{
    public static function register(Exceptions $exceptions): void
    {
        // When to force JSON
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return self::isRestRequest($request) || $request->expectsJson();
        });

        // 404 Not Found
        $exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
            if (! self::isRestRequest($request)) return null;

            return response()->json([
                'success' => false,
                'code'    => 'not_found',
                'message' => 'Endpoint not found',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 404);
        });

        // 405 Method Not Allowed
        $exceptions->renderable(function (MethodNotAllowedHttpException $e, Request $request) {
            if (! self::isRestRequest($request)) return null;

            return response()->json([
                'success' => false,
                'code'    => 'method_not_allowed',
                'message' => 'HTTP method not allowed for this endpoint',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 405);
        });

        // 401 Unauthenticated
        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            if (! self::isRestRequest($request)) return null;

            return response()->json([
                'success' => false,
                'code'    => 'unauthenticated',
                'message' => 'Authentication required',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 401);
        });

        // 403 Forbidden
        $exceptions->renderable(function (AuthorizationException $e, Request $request) {
            if (! self::isRestRequest($request)) return null;

            return response()->json([
                'success' => false,
                'code'    => 'forbidden',
                'message' => 'You are not allowed to perform this action',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 403);
        });

        // 422 Validation errors
        $exceptions->renderable(function (ValidationException $e, Request $request) {
            if (! self::isRestRequest($request)) return null;

            return response()->json([
                'success' => false,
                'code'    => 'validation_error',
                'message' => 'The given data was invalid',
                'errors'  => $e->errors(),
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 422);
        });

        // HttpException (abort(400/500/etc))
        $exceptions->renderable(function (HttpException $e, Request $request) {
            if (! self::isRestRequest($request)) return null;

            $status = $e->getStatusCode();

            return response()->json([
                'success' => false,
                'code'    => 'http_error_' . $status,
                'message' => $e->getMessage() ?: 'HTTP error',
                'debug'   => config('app.debug') ? $e->getTrace() : null,
            ], $status);
        });

        // Unknown / fatal errors (500)
        $exceptions->renderable(function (Throwable $e, Request $request) {
            if (! self::isRestRequest($request)) return null;

            return response()->json([
                'success' => false,
                'code'    => 'server_error',
                'message' => config('app.debug')
                    ? $e->getMessage()
                    : 'Server error. Please try again later.',
                'debug' => config('app.debug') ? $e->getTrace() : null,
            ], 500);
        });
    }

    protected static function isRestRequest(Request $request): bool
    {
        // For: /rest/v1/...
        if ($request->is('rest/*')) return true;

        // For default Laravel API route prefix: /api/rest/...
        if ($request->is('api/rest/*')) return true;

        return false;
    }
}
