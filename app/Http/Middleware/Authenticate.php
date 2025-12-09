<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * For API-only applications, always return null to send JSON 401 response
     * instead of redirecting to a login page.
     */
    protected function redirectTo(Request $request): ?string
    {
        // API-only: Always return null to send JSON 401 Unauthenticated response
        return null;
    }
}
