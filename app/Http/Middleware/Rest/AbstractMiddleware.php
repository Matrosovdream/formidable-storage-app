<?php

namespace App\Http\Middleware\Rest;

class AbstractMiddleware {

    public function returnUnauthorizedResponse() {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

}