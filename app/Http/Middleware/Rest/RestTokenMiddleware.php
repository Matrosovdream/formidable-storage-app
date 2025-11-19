<?php

namespace App\Http\Middleware\Rest;

use App\Services\Site\SiteTokenService;
use Closure;

class RestTokenMiddleware extends AbstractMiddleware
{

    public function __construct(
        protected SiteTokenService $tokenService
    )
    {

    }

    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();

        if (
            !$token ||
            !$this->tokenService->validateToken( $token )
        ) {
            return $this->returnUnauthorizedResponse();
        }

        return $next($request);
    }
}
