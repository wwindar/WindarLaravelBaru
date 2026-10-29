<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AdminTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Menerima dua cara otentikasi redaksi:
     *   1. Header "X-Admin-Token" cocok dengan ADMIN_API_TOKEN di .env
     *   2. Bearer token Sanctum milik user dengan role "admin"
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ── Cara 1: X-Admin-Token header ─────────────────────────────────────
        $staticToken = $request->header('X-Admin-Token');
        $expectedToken = config('app.admin_api_token', env('ADMIN_API_TOKEN', 'redaksi-rahasia-123'));

        if (! empty($staticToken) && ! empty($expectedToken) && hash_equals((string) $expectedToken, (string) $staticToken)) {
            return $next($request);
        }

        // ── Cara 2: Bearer token Sanctum ─────────────────────────────────────
        $bearerToken = $request->bearerToken();

        if (! empty($bearerToken)) {
            $accessToken = PersonalAccessToken::findToken($bearerToken);

            if ($accessToken) {
                $user = $accessToken->tokenable;

                if ($user && $user->isAdmin()) {
                    // Pasang user ke request agar controller bisa akses via $request->user()
                    auth()->setUser($user);

                    return $next($request);
                }
            }
        }

        return response()->json([
            'message' => 'Token redaksi tidak valid atau tidak disertakan.',
        ], 401);
    }
}
