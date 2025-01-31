<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class ForceJsonOnApi.
 */
class ForceJsonOnApi
{
    /**
     * Forces json response on all routes within `/api` path.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (str_contains($request->url(), '/api/')) {
            $request->headers->set('Accept', 'application/json');
        }

        return $next($request);
    }
}
