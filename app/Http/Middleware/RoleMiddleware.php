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
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Oturum açmanız gerekiyor.'], 401);
            }
            return redirect()->route('login');
        }

        $user = auth()->user();
        $allowedRoles = array_map('trim', explode('|', $role));

        if (!in_array($user->role, $allowedRoles)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Bu işlem için yetkiniz yok.'], 403);
            }
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
