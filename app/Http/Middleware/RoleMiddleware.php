<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login', ['locale' => $request->route('locale') ?? 'en']);
        }

        if (! in_array(auth()->user()->role, explode('|', $role))) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
