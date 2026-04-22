<?php

namespace App\Http\Middleware;

use Closure;

class IgnoreInvalidHttpMethodOverride
{
    public function handle(mixed $request, Closure $next): mixed
    {
        $override = $request->input('_method')
            ?? $request->header('X-HTTP-Method-Override');

        $allowed = ['PUT', 'PATCH', 'DELETE'];

        if ($override && !in_array(strtoupper($override), $allowed)) {
            // Ungültige Overrides entfernen → verhindert die Exception
            $request->request->remove('_method');
            $request->headers->remove('X-HTTP-Method-Override');
        }

        return $next($request);
    }
}
