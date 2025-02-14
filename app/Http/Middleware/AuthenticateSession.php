<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class AuthenticateSession.
 */
class AuthenticateSession extends \Illuminate\Session\Middleware\AuthenticateSession
{
    /**
     * Get the path the user should be redirected to when their session is not authenticated.
     *
     * @param Request $request
     *
     * @return string|null
     * @SuppressWarnings(PHPMD)
     */
    protected function redirectTo(Request $request): ?string
    {
        return null;
    }
}
