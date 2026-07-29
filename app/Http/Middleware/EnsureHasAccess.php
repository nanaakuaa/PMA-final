<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure user is authenticated
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Check if user has access to the resource
        $routeParameters = $request->route()->parameters();

        foreach ($routeParameters as $parameter) {
            if (is_object($parameter) && method_exists($parameter, 'user_id')) {
                if ($parameter->user_id !== $request->user()->id) {
                    abort(403, 'Unauthorized access to this resource');
                }
            }
        }

        return $next($request);
    }
}
